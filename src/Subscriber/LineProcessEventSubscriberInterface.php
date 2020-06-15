<?php

declare(strict_types=1);


namespace App\Subscriber;


interface LineProcessEventSubscriberInterface
{
    /**
     * @return string the event type targeted by the listener
     */
    public function getTargetEventType(): string;
}
