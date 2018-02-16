<?php

declare(strict_types=1);

namespace presentkim\playerapi\lang;

interface Translation{

    /**
     * @return PluginLang
     */
    public function getLanguage() : PluginLang;

    /**
     * @return string
     */
    public function getLangId() : string;

    /**
     * @param null|string $id     = null
     * @param string[]    $params = []
     *
     * @return string
     */
    public function translate(?string $id = null, array $params = []) : string;
}