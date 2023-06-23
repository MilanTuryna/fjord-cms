<?php

namespace App\Model\Security\Auth;

use App\Model\Database\Repository\Admin\Entity\Account as Admin;
use App\Model\Database\Repository\Admin\AccessLogRepository;
use App\Model\Database\Repository\Admin\AccountRepository;
use App\Model\Database\Repository\Admin\Entity\AccessLog;
use App\Model\Security\Auth\Exceptions\BadCredentialsException;
use App\Model\Security\Auth\Exceptions\LoggedOutException;
use Nette\Database\Table\ActiveRow;
use Nette\Http\Session;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;

/**
 * Class AdminAuthenticator
 * @package App\Model\Security\SessionAuth
 */
class AdminAuthenticator implements IAuthenticator
{
    const SESSION_SECTION = 'admin_login';

    private AccessLogRepository $logRepository;
    private AccountRepository $adminRepository;
    private Passwords $passwords;
    private Session $session;

    /**
     * Authenticator constructor.
     * @param AccessLogRepository $logRepository
     * @param AccountRepository $adminRepository
     * @param Passwords $passwords
     * @param Session $session
     */
    public function __construct(AccountRepository $adminRepository, AccessLogRepository $logRepository, Passwords $passwords, Session $session)
    {
        $this->adminRepository = $adminRepository;
        $this->logRepository = $logRepository;
        $this->passwords = $passwords;
        $this->session = $session;
    }

    /**
     * @param array $credentials
     * @param string $expiration
     * @throws BadCredentialsException
     */
    public function login(array $credentials, string $expiration = IAuthenticator::EXPIRATION): void
    {
        [$email, $password] = $credentials;
        /**
         * @var $admin Admin
         */
        $admin = $this->adminRepository->findByColumn("email", $email)->fetch();

        $section = $this->session->getSection(self::SESSION_SECTION);
        $section->setExpiration($expiration);

        if ($admin && $this->passwords->verify($password, $admin->password)) { // log in
            $accessLog = [
                AccessLog::admin_id => $admin->id,
                AccessLog::device => $_SERVER["HTTP_USER_AGENT"],
                AccessLog::ip => $_SERVER["REMOTE_ADDR"],
                AccessLog::created => new DateTime(),
            ];
            $this->logRepository->insert($accessLog);
            $section['id'] = $admin->id;
        } else {
            throw new BadCredentialsException();
        }
    }

    public function getId(): ?int
    {
        $id = $this->session->getSection(self::SESSION_SECTION)['id'];
        if (!$id) return null;
        return $id;
    }

    /**
     * @return ActiveRow|null
     */
    public function getUser(): ?ActiveRow
    {
        $id = $this->getId();
        return $id ? $this->adminRepository->findById($id) : null;
    }

    /**
     * @throws LoggedOutException
     */
    public function logout(): void
    {
        $section = $this->session->getSection(self::SESSION_SECTION);
        if (!$section['id']) throw new LoggedOutException();
        $section->remove();
    }

    public function getPasswords(): Passwords {
        return $this->passwords;
    }
}