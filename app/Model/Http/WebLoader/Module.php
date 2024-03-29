<?php


namespace App\Model\Http\WebLoader;


use App\Model\Http\WebLoader\Parsers\CSSParser;
use App\Model\Http\WebLoader\Parsers\Exceptions\SyntaxError;
use App\Model\Http\WebLoader\Parsers\JSParser;
use ParseError;

/**
 * Class Module
 * @package App\Model\Http\WebLoader
 */
abstract class Module
{
    private FileMask $cssMask;
    private FileMask $jsMask;
    private ?string $moduleName;

    public string $basePath = '';

    /**
     * Module constructor.
     * @param FileMask $cssMask
     * @param FileMask $jsMask
     * @param string|null $moduleName
     */
    public function __construct(FileMask $cssMask, FileMask $jsMask, ?string $moduleName = null)
    {
        $this->cssMask = $cssMask;
        $this->jsMask = $jsMask;
        $this->moduleName = $moduleName;
    }

    /**
     * @return CSSParser
     * @throws ParseError
     */
    public function getParsedCSS(): CSSParser
    {
        $rawContent = $this->cssMask->scrapFiles();
        try {
            $cssParser = new CSSParser($rawContent, null, false);
            $cssParser->setBasePath($this->basePath);
            return $cssParser
                ->addComment('fsize: ' . strlen($rawContent))
                ->addComment('web-loader module: ' . $this->moduleName ?? 'undefined name');
        } catch (SyntaxError $e) {
            throw new ParseError("An error (SyntaxError) occurred while parsing CSS code in [" . implode(',', $this->cssMask->getMasks()) . "] files. " . $e->getMessage(), $e);
        }
    }

    /**
     * @return string
     * @throws ParseError
     */
    public function getParsedJS(): string {
        $rawContent = $this->jsMask->scrapFiles();
        $jsParser = new JSParser($rawContent);
        try {
            return $jsParser->getCompiledCode();
        } catch (\Exception $exception) {
            throw new ParseError("An error occured while parsing JS code in [". implode(',', $this->cssMask->getMasks()) . "] files. " . $exception->getMessage(), $exception);
        }
    }
}