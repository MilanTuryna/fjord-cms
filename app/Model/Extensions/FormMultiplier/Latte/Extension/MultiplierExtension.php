<?php declare(strict_types = 1);

namespace App\Model\Extensions\FormMultiplier\Latte\Extension;

use App\Model\Extensions\FormMultiplier\Latte\Extension\Node\MultiplierAddNode;
use App\Model\Extensions\FormMultiplier\Latte\Extension\Node\MultiplierNode;
use App\Model\Extensions\FormMultiplier\Latte\Extension\Node\MultiplierRemoveNode;
use Latte\Extension;

final class MultiplierExtension extends Extension
{

	/**
	 * @return array<string, callable>
	 */
	public function getTags(): array
	{
		return [
			'multiplier' => [MultiplierNode::class, 'create'],
			'n:multiplier' => [MultiplierNode::class, 'create'],
			'multiplier:remove' => [MultiplierRemoveNode::class, 'create'],
			'multiplier:add' => [MultiplierAddNode::class, 'create'],
		];
	}

}
