<?php

declare(strict_types=1);

namespace shura62\neptune\processing\types;

use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use shura62\neptune\processing\Processor;
use shura62\neptune\user\User;

class PacketProcessor extends Processor {

    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function process(DataPacket $packet) : void{
        $user = $this->user;
        switch($packet->pid()) {
            case ProtocolInfo::INTERACT_PACKET:
                if($packet->action === InteractPacket::ACTION_OPEN_INVENTORY)
                    $user->inventoryOpen = true;
                break;
            case ProtocolInfo::CONTAINER_CLOSE_PACKET:
                $user->inventoryOpen = false;
                break;
            case ProtocolInfo::PLAYER_ACTION_PACKET:
                switch($packet->action) {
                    case PlayerActionPacket::ACTION_ABORT_BREAK:
                    case PlayerActionPacket::ACTION_STOP_BREAK:
                        $user->digging = false;
                        break;
                    case PlayerActionPacket::ACTION_START_BREAK:
                    case PlayerActionPacket::ACTION_CONTINUE_BREAK:
                        $user->digging = true;
                        break;
                    case PlayerActionPacket::ACTION_START_SPRINT:
                        $user->sprinting = true;
                        break;
                    case PlayerActionPacket::ACTION_STOP_SPRINT:
                        $user->sprinting = false;
                        break;
                }
                break;
        }
    }

}