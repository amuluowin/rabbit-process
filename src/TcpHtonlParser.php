<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 23:19
 */

namespace rabbit\process;


use rabbit\helper\JsonHelper;
use rabbit\parser\ParserInterface;
use rabbit\socket\tcp\TcpParserInterface;

/**
 * Class TcpHtonlParser
 * @package rabbit\process
 */
class TcpHtonlParser implements TcpParserInterface
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * TcpHtonlParser constructor.
     * @param ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function encode(array $data): string
    {
        $data = $this->parser->encode(JsonHelper::encode($data, JSON_UNESCAPED_UNICODE));
        return htonl(strlen($data)) + $data;
    }

    public function decode(string $data)
    {
        $data = $this->parser->decode(ltrim($data, htonl(strlen($data))));
        $data = JsonHelper::decode($data, true);
        return $data;
    }

}