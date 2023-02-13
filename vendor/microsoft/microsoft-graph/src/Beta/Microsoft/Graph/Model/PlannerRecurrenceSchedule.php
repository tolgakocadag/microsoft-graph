<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* PlannerRecurrenceSchedule File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
namespace Beta\Microsoft\Graph\Model;
/**
* PlannerRecurrenceSchedule class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class PlannerRecurrenceSchedule extends Entity
{

    /**
    * Gets the nextOccurrenceDateTime
    *
    * @return \DateTime|null The nextOccurrenceDateTime
    */
    public function getNextOccurrenceDateTime()
    {
        if (array_key_exists("nextOccurrenceDateTime", $this->_propDict)) {
            if (is_a($this->_propDict["nextOccurrenceDateTime"], "\DateTime") || is_null($this->_propDict["nextOccurrenceDateTime"])) {
                return $this->_propDict["nextOccurrenceDateTime"];
            } else {
                $this->_propDict["nextOccurrenceDateTime"] = new \DateTime($this->_propDict["nextOccurrenceDateTime"]);
                return $this->_propDict["nextOccurrenceDateTime"];
            }
        }
        return null;
    }

    /**
    * Sets the nextOccurrenceDateTime
    *
    * @param \DateTime $val The value to assign to the nextOccurrenceDateTime
    *
    * @return PlannerRecurrenceSchedule The PlannerRecurrenceSchedule
    */
    public function setNextOccurrenceDateTime($val)
    {
        $this->_propDict["nextOccurrenceDateTime"] = $val;
         return $this;
    }

    /**
    * Gets the pattern
    *
    * @return RecurrencePattern|null The pattern
    */
    public function getPattern()
    {
        if (array_key_exists("pattern", $this->_propDict)) {
            if (is_a($this->_propDict["pattern"], "\Beta\Microsoft\Graph\Model\RecurrencePattern") || is_null($this->_propDict["pattern"])) {
                return $this->_propDict["pattern"];
            } else {
                $this->_propDict["pattern"] = new RecurrencePattern($this->_propDict["pattern"]);
                return $this->_propDict["pattern"];
            }
        }
        return null;
    }

    /**
    * Sets the pattern
    *
    * @param RecurrencePattern $val The value to assign to the pattern
    *
    * @return PlannerRecurrenceSchedule The PlannerRecurrenceSchedule
    */
    public function setPattern($val)
    {
        $this->_propDict["pattern"] = $val;
         return $this;
    }

    /**
    * Gets the patternStartDateTime
    *
    * @return \DateTime|null The patternStartDateTime
    */
    public function getPatternStartDateTime()
    {
        if (array_key_exists("patternStartDateTime", $this->_propDict)) {
            if (is_a($this->_propDict["patternStartDateTime"], "\DateTime") || is_null($this->_propDict["patternStartDateTime"])) {
                return $this->_propDict["patternStartDateTime"];
            } else {
                $this->_propDict["patternStartDateTime"] = new \DateTime($this->_propDict["patternStartDateTime"]);
                return $this->_propDict["patternStartDateTime"];
            }
        }
        return null;
    }

    /**
    * Sets the patternStartDateTime
    *
    * @param \DateTime $val The value to assign to the patternStartDateTime
    *
    * @return PlannerRecurrenceSchedule The PlannerRecurrenceSchedule
    */
    public function setPatternStartDateTime($val)
    {
        $this->_propDict["patternStartDateTime"] = $val;
         return $this;
    }
}
