<?php

declare(strict_types=1);

namespace Symfony\Component\Notifier\Bridge\FakeSms;

interface SmsInterface
{
    public function getId(): string;

    public function setSmsData(string $from, string $to, string $text): void;
}
