<?php

namespace OCA\AutomaticMediaEncoder\Notification;

use InvalidArgumentException;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\L10N\IFactory as ILocalizationFactory;
use OCP\Notification\IManager as INotificationManager;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;
use OCA\AutomaticMediaEncoder\AppInfo\Application;

class Notifier implements INotifier
{
    protected ILocalizationFactory $localizationFactory;
    protected IUserManager $userManager;
    protected INotificationManager $notificationManager;
    protected IURLGenerator $urlGenerator;

    public function __construct(ILocalizationFactory $localizationFactory, IUserManager $userManager, INotificationmanager $notificationManager, IUrlGenerator $urlGenerator)
    {
        $this->localizationFactory = $localizationFactory;
        $this->userManager = $userManager;
        $this->notificationManager = $notificationManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function getId(): string
    {
        return Application::APP_ID;
    }

    public function getName(): string
    {
        return $this->localizationFactory->get(Application::APP_ID)->t('Automatic Media Encoder');
    }

    public function prepare(INotification $notification, string $languageCode): INotification
    {
        if ($notification->getApp() !== Application::APP_ID) {
            throw new \InvalidArgumentException("Notification sent to the wrong app");
        }

        $l = $this->factory->get(Application::APP_ID, $languageCode);

        if ($notification->getSubject() !== 'rule_encoding_finished') {
            throw new InvalidArgumentException();
        }

        $subjectParameters = $notification->getSubjectParameters();

        $targetPath = $subjectParameters['targetPath'];

        $converted = [
            'videosEncoded' => (int) $subjectParameters['videosEncoded'],
            'photosEncoded' => (int) $subjectParameters['photosEncoded'],
            'audiosEncoded' => (int) $subjectParameters['audiosEncoded']
        ];

        $messages = [];
        if ($converted['videosEncoded'] > 0) {
            $messages[] = $l->n('%s video encoded', '%s videos encoded', $converted['videosEncoded']);
        }
        if ($converted['photosEncoded'] > 0) {
            $messages[] = $l->n('%s photo encoded', '%s photos encoded', $converted['photosEncoded']);
        }
        if ($converted['audiosEncoded'] > 0) {
            $messages[] = $l->n('%s audio encoded', '%s audios encoded', $converted['audiosEncoded']);
        }
        $content = $l->t(implode(', ', $messages));

        return $notification
            ->setParsedSubject($content)
            ->setIcon($this->url->getAbsoluteURL($this->url->imagePath(Application::APP_ID, 'icon.svg')))
            ->setLink($this->url->linkToRouteAbsolute('files.view.index', ['dir' => $targetPath]));
    }
}
