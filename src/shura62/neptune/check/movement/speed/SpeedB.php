<?php

declare(strict_types=1);

namespace shura62\neptune\check\movement\speed;

use pocketmine\entity\Effect;
use shura62\neptune\check\Check;
use shura62\neptune\event\PacketReceiveEvent;
use shura62\neptune\user\User;
use shura62\neptune\utils\packet\Packets;
use shura62\neptune\utils\PlayerUtils;

class SpeedB extends Check {

    public function __construct() {
        parent::__construct("Speed", "BHop");
    }

    public function onPacket(PacketReceiveEvent $e, User $user) {
        if (!$e->equals(Packets::MOVE))
            return;
        $dist = hypot($user->velocity->getX(), $user->velocity->getZ());
        $lastDist = hypot($user->lastVelocity->getX(), $user->lastVelocity->getZ());

        $prediction = $lastDist * 0.699999988079071;
        $diff = abs($dist - $prediction);
        $scaledDist = $diff * 100;

        $max = 11 + (PlayerUtils::getPotionEffectLevel($e->getPlayer(), Effect::SPEED) * 0.2);

        if ($scaledDist > $max
                && $user->airTicks > 4
                && !$user->getPlayer()->getAllowFlight()) {
            if (++$this->vl > 3)
                $this->flag($user, "dist= " . $scaledDist);
        } else $this->vl = 0;
    }

}