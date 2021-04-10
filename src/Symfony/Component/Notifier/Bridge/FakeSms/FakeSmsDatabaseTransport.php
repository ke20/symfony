<?php

declare(strict_types=1);

namespace Symfony\Component\Notifier\Bridge\FakeSms;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Exception\InvalidArgumentException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Webmozart\Assert\Tests\StaticAnalysis\throws;

class FakeSmsDatabaseTransport extends AbstractTransport
{
    private $entityManager;
    private $entity;
    private $to;
    private $from;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $entity,
        string $to,
        string $from,
        HttpClientInterface $client = null,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
        $this->to = $to;
        $this->from = $from;

        parent::__construct($client, $dispatcher);
    }

    public function __toString(): string
    {
        return sprintf('fakesms+database://%s?to=%s&from=%s', $this->getEndpoint(), $this->to, $this->from);
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage;
    }

    /**
     * @param MessageInterface|SmsMessage $message
     *
     * @throws InvalidArgumentException
     */
    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$this->supports($message)) {
            throw new UnsupportedMessageTypeException(__CLASS__, SmsMessage::class, $message);
        }

        if (!class_exists($this->entity)) {
            throw new InvalidArgumentException(sprintf('The given class "%s" does not exist', $this->entity));
        }

        $smsEntity = new $this->entity();
        if (!$smsEntity instanceof SmsInterface) {
            throw new InvalidArgumentException(sprintf('Given class of type "%s", expected "%s"', get_class($smsEntity), SmsInterface::class));
        }

        $smsEntity->setSmsData($this->from, $this->to, $message->getSubject());
        $this->entityManager->persist($smsEntity);
        $this->entityManager->flush();

        $sentMessage = new SentMessage($message, (string) $this);
        $sentMessage->setMessageId($smsEntity->getId());

        return $sentMessage;
    }
}
