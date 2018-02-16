<?php

declare(strict_types=1);

namespace presentkim\playerapi\lang;

use presentkim\playerapi\PlayerAPI;

/**
 * This trait implements most methods in the {@link Translation} interface.
 */
trait TranslationTrait{

    /**
     * @return PluginLang
     */
    public function getLanguage() : PluginLang{
        return PlayerAPI::getInstance()->getLanguage();
    }

    /**
     * @param null|string $id     = null
     * @param string[]    $params = []
     *
     * @return string
     */
    public function translate(?string $id = null, array $params = []) : string{
        return $this->getLanguage()->translate($this->getLangId() . (empty($id) ? '' : ".{$id}"), $params);
    }
}