<?php
/**
 * z z a p l i b   3   m i n i   f r a m e w o r k
 * ===============================================
 *
 * This library is a mini framework from php web applications
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 3 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 */

namespace dollmetzer\zzaplib\translator;

use dollmetzer\zzaplib\Config;
use dollmetzer\zzaplib\logger\LoggerInterface;
use dollmetzer\zzaplib\exception\ApplicationException;

/**
 * Class Translator
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib
 */
class Translator implements TranslatorInterface
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array Language snippets
     */
    protected $snippets = [];


    /**
     * Translator constructor.
     *
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(Config $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Import language snippets from .ini file
     *
     * @param string $language
     * @param string $module
     * @param string $controller
     * @return bool
     * @throws ApplicationException
     */
    public function importLanguage(string $language, string $module = 'index', string $controller = 'core'): bool
    {
        $filename = PATH_APP . 'modules/' . $module . '/data/lang_' . $controller . '_' . $language . '.ini';

        if (file_exists($filename)) {
            $newSnippets = parse_ini_file($filename, true);
            $this->snippets = array_merge($this->snippets, $newSnippets);
            return true;
        } else {
            $this->logger->warning('Translator could not import language file', ['file' => $filename]);
            return false;
        }
    }

    /**
     * return a language snippet for a key
     *
     * @param string $key
     * @return string Snippet or marker, that indicates a missing translation
     */
    public function translate(string $key): string
    {
        if (key_exists($key, $this->snippets)) {
            return $this->snippets[$key];
        } else {
            return '###_' . strtoupper($key) . '_###';
        }
    }
}
