<?php


namespace App\Forms\Front;


use App\Forms\FlashMessages;
use App\Forms\Form;
use App\Forms\FormRedirect;
use App\Forms\Front\Data\ContactFormData;
use App\Model\Database\Repository\Product\ProductRepository;
use App\Model\Database\Repository\Settings\GlobalSettingsRepository;
use App\Model\Database\Repository\SMTP\Entity\Mail;
use App\Model\Database\Repository\SMTP\Entity\Server;
use App\Model\Database\Repository\SMTP\MailRepository;
use App\Model\Database\Repository\SMTP\ServerRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Nette\Utils\Html;

class ProductForm extends Form
{
    /**
     * ProductForm constructor.
     * @param Presenter $presenter
     * @param ServerRepository $serverRepository
     * @param MailRepository $mailRepository
     * @param ProductRepository $productRepository
     * @param GlobalSettingsRepository $globalSettingsRepository
     * @param int $productId
     */
    #[Pure] public function __construct(Presenter $presenter, private ServerRepository $serverRepository, private MailRepository $mailRepository, private ProductRepository $productRepository, private GlobalSettingsRepository $globalSettingsRepository, private int $productId,)
    {
        parent::__construct($presenter);

        $this->presenter = $presenter;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form {
        $form = parent::create();
        $form->addText("name", "Vaše jméno/Firma")->setRequired("Zadejte vaše jméno či název firmy.");
        $form->addEmail("email", "E-mail")->setRequired("Zadejte emailovou adresu odpovídající formátu.");
        $form->addTextArea("content", "Obsah")->setRequired("Zadejte obsah poptávky, kterou chcete odeslat.");
        $form->addSubmit("submit", "Odeslat");
        return $form;
    }

    public function success(\Nette\Application\UI\Form $form, ContactFormData $values): void {
        /**
         * @var $server Server
         */
        $settings = $this->globalSettingsRepository->getActualSettings();
        $server = $this->serverRepository->findByColumn("active", 1)->fetch();
        $product = $this->productRepository->findById($this->productId);
        if(!$server) $form->addError("Nastala chyba na straně serveru. Zkuste odeslat email ručně.");
        try {
            $message = new Message();
            $productLink = $_SERVER['HTTP_REFERER'];
            $message->setFrom(sprintf("%s <%s>", $server->name, $server->server_email))->addTo($server->receiver_email)->setSubject("Nová poptávka na produkt: " . $settings->app_name)
                ->setHtmlBody("Tato poptávka přišla od <b>" . $values->name . " (" . $values->email . ")</b>. Jedná se o produkt: <a href='" . $productLink . "' target='blank'>" . $product->title . " (č. " . $product->id . ")</a><br><br>" . Html::htmlToText($values->content));
            $mailer = new SmtpMailer(
                host: $server->server_host,
                username: $server->server_email,
                password: $server->server_password,
                encryption: 'ssl',
            );
            $mailer->send($message);
            $mail = new Mail();
            $mail->content = $values->content;
            $mail->title = $values->name . ": " . $values->email;
            $mail->server_id = $server->id;
            $mail->original_sender = $values->email;
            $this->mailRepository->insert($mail->iterable());
            $this->presenter->flashMessage("Poptávka byla úspěšně odeslána. Odpovíme na ní co nejdříve.", FlashMessages::SUCCESS);
            $this->presenter->redirectUrl($productLink . "?success");
        } catch (\Exception $exception) {
            throw $exception;
            $this->presenter->flashMessage("Z neznámého důvodu se nepodařilo email odeslat. Zkuste to ručně.", FlashMessages::ERROR);
        }
    }
}