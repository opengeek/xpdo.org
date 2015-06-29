<?php
/*
 * This file is part of the xpdo.org package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace xPDO\DotOrg\Validation;


use xPDO\Validation\xPDOValidationRule;

class StabilityEnum extends xPDOValidationRule
{
    public function isValid($value, array $options = [])
    {
        $result = false;
        parent::isValid($value, $options);

        $result = in_array($value, ['stable', 'alpha', 'beta', 'RC'], true);

        if ($result === false) {
            $this->validator->addMessage($this->field, $this->name, $this->message);
        }
        return $result;
    }
}
