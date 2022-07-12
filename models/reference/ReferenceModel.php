<?php

namespace Sonder\Models;

use Sonder\Core\CoreModel;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IModel;
use Sonder\Models\Reference\Exceptions\ReferenceException;
use Sonder\Models\Reference\Exceptions\ReferenceModelException;
use Sonder\Models\Reference\Interfaces\IReferenceModel;
use Sonder\Models\Reference\Interfaces\IReferenceStore;
use Sonder\Models\Reference\Interfaces\IReferenceValuesObject;
use Sonder\Models\Reference\ValuesObjects\ReferenceValuesObject;
use Sonder\Plugins\UuidPlugin;

/**
 * @property null $api
 * @property IReferenceStore $store
 */
#[IModel]
#[IReferenceModel]
final class ReferenceModel extends CoreModel implements IReferenceModel
{
    private const COLLISION_TRY_COUNT = 10;

    /**
     * @param bool $uniqueCheck
     * @return string
     * @throws CoreException
     * @throws ReferenceModelException
     */
    final public function create(bool $uniqueCheck = true): string
    {
        $reference = $this->_getRandomReference();

        if (!$uniqueCheck) {
            return $reference;
        }

        $try = 1;

        while ($this->getVOByReference($reference) !== null) {
            if ($try >= self::COLLISION_TRY_COUNT) {
                throw new ReferenceModelException(
                    ReferenceModelException::MESSAGE_MODEL_COLLISION,
                    ReferenceException::CODE_MODEL_COLLISION
                );
            }

            $reference = $this->_getRandomReference();

            $try++;
        }

        $this->store->insertReference($reference);

        return $reference;
    }

    /**
     * @param string|null $reference
     * @return IReferenceValuesObject|null
     */
    final public function getVOByReference(
        ?string $reference = null
    ): ?IReferenceValuesObject {
        $row = $this->store->getReferenceRowByReference($reference);

        if (!empty($row)) {
            return new ReferenceValuesObject($row);
        }

        return null;
    }

    /**
     * @param string|null $reference
     * @return bool
     */
    final public function removeByReference(?string $reference = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteReferenceByReference($reference);
    }

    /**
     * @return string
     * @throws CoreException
     */
    private function _getRandomReference(): string
    {
        /* @var $uuidPlugin UuidPlugin */
        $uuidPlugin = $this->getPlugin('uuid');

        return $uuidPlugin->doGenerate();
    }
}


