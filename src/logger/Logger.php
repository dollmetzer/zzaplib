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

namespace dollmetzer\zzaplib\logger;

use dollmetzer\zzaplib\Config;

/**
 * Class Logger
 *
 * Compact PSR-3 compatible logger class
 *
 * @author Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @copyright 2006 - 2019 Dirk Ollmetzer (dirk.ollmetzer@ollmetzer.com)
 * @package dollmetzer\zzaplib\logger
 */
class Logger implements LoggerInterface
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $message;

    /**
     * Logger constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->message = '';
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function emergency($message, array $context = [])
    {
        $this->log('emergency', $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function alert($message, array $context = [])
    {
        $this->log('alert', $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function critical($message, array $context = [])
    {
        $this->log('critical', $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function error($message, array $context = [])
    {
        $this->log('error', $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function warning($message, array $context = [])
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function notice($message, array $context = [])
    {
        $this->log('notice', $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function info($message, array $context = [])
    {
        $this->log('info', $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function debug($message, array $context = [])
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     * @throws \dollmetzer\zzaplib\exception\ApplicationException
     */
    public function log($level, $message, array $context = [])
    {
        $this->message = strftime('%Y-%m-%d %H:%M:%S', time()) . ' [' . strtoupper($level) . '] ' . $message;

        if (!empty($context)) {
            $this->message .= ' ' . str_replace("\n", ' ', print_r($context, true));
        }

        if ($this->config->isSet('logTo', 'application')) {
            $logTo = $this->config->get('logTo', 'application');
        } else {
            $logTo = 'file';
        }

        switch ($logTo) {
            case 'null':
                break;

            default:
                $this->toFile();
        }
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Write logmessage to file
     */
    private function toFile()
    {
        $logfile = PATH_LOGS . strftime('application_log_%Y_%m_%d.txt', time());
        if (!file_exists($logfile)) {
            $fp = fopen($logfile, 'w+');
            fwrite($fp, "Logfile $logfile\n--------------------\n");
            fclose($fp);
            chmod($logfile, 0664);
        }
        $fp = fopen($logfile, 'a+');
        fwrite($fp, $this->message . "\n");
        fclose($fp);
    }
}
