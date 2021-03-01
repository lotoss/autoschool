<?php

namespace Ninja;

class Markdown
{
    private $string;

    public function __construct($markDown)
    {
        $this->string = $markDown;
    }

    public function toHtml()
    {
        // converte $this->string in HTML
        $text = htmlspecialchars($this->string, ENT_QUOTES, 'UTF-8');

        // strong(bold, grassetto)
        $text = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);

        // emphasis (italic, corsivo)
        $text = preg_replace('/_([^_]+)_/', '<em>$1</em>', $text);
        $text = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $text);

        // Converte Windows (\r\n) in Unix (\n)
        $text = str_replace("\r\n", "\n", $text);
        // Converte Machintosh (\r) in Unix (\n)
        $text = str_replace("\r", "\n", $text);

        // [testo del link](URL del link)
        $text = preg_replace('/\[([^\]]+)]\(([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\)/i', '<a href="$2">$1</a>', $text);

        return $text;
    }
}
