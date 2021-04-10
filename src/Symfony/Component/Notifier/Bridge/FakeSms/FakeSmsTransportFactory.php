<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\FakeSms;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Component\Notifier\Transport\TransportInterface;

/**
 * @author James Hemery <james@yieldstudio.fr>
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class FakeSmsTransportFactory extends AbstractTransportFactory
{
    protected $mailer;
    protected $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    /**
     * @return FakeSmsEmailTransport
     */
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();

        if (!\in_array($scheme, $this->getSupportedSchemes())) {
            throw new UnsupportedSchemeException($dsn, 'fakesms', $this->getSupportedSchemes());
        }

        if ('fakesms+email' === $scheme) {
            $mailerTransport = $dsn->getHost();
            $to = $dsn->getRequiredOption('to');
            $from = $dsn->getRequiredOption('from');

            return (new FakeSmsEmailTransport($this->mailer, $to, $from))->setHost($mailerTransport);
        }

        if ('fakesms+database' === $scheme) {
            return (new FakeSmsDatabaseTransport(
                $this->entityManager,
                $dsn->getRequiredOption('entity'),
                $dsn->getRequiredOption('to'),
                $dsn->getRequiredOption('from')
            ))->setHost($dsn->getHost());
        }
    }

    protected function getSupportedSchemes(): array
    {
        return ['fakesms+email', 'fakesms+database'];
    }
}
