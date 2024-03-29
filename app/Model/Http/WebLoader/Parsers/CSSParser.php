<?php


namespace App\Model\Http\WebLoader\Parsers;

use App\Model\Http\WebLoader\Parsers\Exceptions\SyntaxError;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS;
use Sabberworm\CSS\Parser;

/**
 * Code parser for better manipulation with user-written code in forms.
 * Class CSSParser
 * @package App\Model\Front\Parsers
 */
class CSSParser
{
    const CLASS_SELECTOR = '.';
    const BASEPATH_VAR = '{$basePath}';

    private string $rawCode;
    private ?string $class;
    private bool $strictParsing;
    private array $comments;

    private Document $document;

    /**
     * CSSParser constructor.
     * @param string $rawCode
     * @param string|null $class
     * @param bool $strictParsing
     * @throws SyntaxError
     */
    public function __construct(string $rawCode, ?string $class, bool $strictParsing = true)
    {
        $this->rawCode = $rawCode;
        $this->class = $class;
        $this->strictParsing = $strictParsing;
        $this->comments = [];
        $parser = new Parser($this->rawCode, $this->strictParsing ? CSS\Settings::create()->beStrict() : null);
        try {
            $this->document = $parser->parse();
        } catch (CSS\Parsing\SourceException $sourceException) { // catching CSS\Parsing exceptions for IDE hinting
            throw new SyntaxError($sourceException->getMessage(), $sourceException->getLine());
        }
    }

    /**
     * @param $comment
     * @return CSSParser
     */
    public function addComment($comment): self
    {
        $this->comments[] = $comment;
        return $this;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath(string $basePath): void
    {
        foreach ($this->document->getAllValues() as $value) {
            if ($value instanceof CSS\Value\URL) {
                $valURL = $value->getURL();
                $valURL->setString(str_replace(self::BASEPATH_VAR, $basePath, $valURL->getString()));
                $value->setURL($valURL);
            }
        }
    }

    /**
     * Removing all selectors except $this->class
     * @return array
     */
    public function removeDisabledSelectors(): array
    {
        $deletedSelectors = [];
        if ($this->class) {
            foreach ($this->document->getAllDeclarationBlocks() as $block) {
                /**
                 * @var $block CSS\RuleSet\DeclarationBlock
                 * @var $oSelector CSS\Property\Selector
                 */

                foreach ($block->getSelectors() as $oSelector) if (!str_contains($oSelector, self::CLASS_SELECTOR . $this->class)) {
                    array_push($deletedSelectors, $oSelector);
                    $block->removeSelector($oSelector);
                }
            }
        }
        return $deletedSelectors;
    }

    /**
     * @param bool $minify
     * @return string
     */
    public function getComputedCode(bool $minify = true): string
    {
        try {
            $format = $minify ? CSS\OutputFormat::createCompact() : null;
            $computedCode = $this->document->render($format);
            $comments = "/*\n";
            foreach ($this->comments as $comment) $comments .= "$comment\n";
            $comments .= "\n*/\n\n";
            return $comments . $computedCode;
        } catch (CSS\Parsing\OutputException $outputException) {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getRawCode(): string
    {
        return $this->rawCode;
    }

    public function __toString(): string
    {
        $this->getComputedCode(true);
    }
}