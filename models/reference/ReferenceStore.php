<?php

namespace Sonder\Models\Reference;

use Sonder\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Models\Reference\Interfaces\IReferenceStore;

#[IModelStore]
#[IReferenceStore]
final class ReferenceStore extends ModelStore implements IReferenceStore
{
    final protected const SCOPE = 'reference';

    private const REFERENCES_TABLE = 'references';

    /**
     * @param string|null $reference
     * @return array|null
     */
    final public function getReferenceRowByReference(
        ?string $reference = null
    ): ?array {
        if (empty($reference)) {
            return null;
        }

        $sqlWhere = sprintf(
            '"references"."reference" = \'%s\'',
            $reference
        );

        $sql = '
            SELECT "references".*
            FROM "%s" AS "references"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf(
            $sql,
            ReferenceStore::REFERENCES_TABLE,
            $sqlWhere
        );

        return $this->getRow($sql);
    }

    /**
     * @param string|null $reference
     * @return bool
     */
    final public function deleteReferenceByReference(
        ?string $reference = null
    ): bool {
        if (empty($reference)) {
            return false;
        }

        return $this->deleteRowByReference(
            ReferenceStore::REFERENCES_TABLE,
            $reference
        );
    }

    /**
     * @param string|null $reference
     * @return bool
     */
    final public function insertReference(?string $reference = null): bool
    {
        if (empty($reference)) {
            return false;
        }

        $row = [
            'reference' => $reference
        ];

        return $this->addRow(ReferenceStore::REFERENCES_TABLE, $row);
    }
}
