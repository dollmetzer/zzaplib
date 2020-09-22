<?php

namespace dollmetzer\zzaplib\translator;

interface TranslatorInterface
{

    public function importLanguage(string $language, string $module = 'index', string $controller = 'index'): bool;

    public function translate(string $key): string;
}
