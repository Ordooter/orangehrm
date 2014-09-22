<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */
//<datapoint type = "count">
//<settings>
//        <name>user_count</name>
//    </settings>
//    <parameters>
//        <table>ohrm_user</table>
//        <where>
//        <connector>
//        <column></column>
//        <comparator></comparator>
//        <value> </value>
//        </where>
//    </parameters>
//</datapoint>
class countDataPointProcessor extends AbstractBaseProcessor {

    public function process($definition) {
        if (!isset($definition)) {
            return array();
        }
        $result = array();
        $datapoint = new SimpleXMLElement($definition);
        if ($datapoint['type'] == 'count') {

            try {

                $query = 'SELECT COUNT(*) FROM ' . $datapoint->parameters->table;

                if (count($datapoint->parameters->where) > 0) {
                    $query .= ' WHERE ';
                }
                $whereFilter = '';
                foreach ($datapoint->parameters->where as $whereClause) {
                    $whereQuery = $datapoint->parameters->where->column .' '. $datapoint->parameters->where->operation .' '. $datapoint->parameters->value;
                    if (empty($datapoint->parameters->where->connector)) {
                        $whereFilter = $whereQuery . ' ' . $whereFilter;
                    } else {
                        $whereFilter .= ' ' . $datapoint->parameters->where->connector . '  ' . $whereQuery;
                    }
                }
                $query = $query.$whereFilter;
                
                $pdo = Doctrine_Manager::connection()->getDbh();
                $query = $pdo->prepare($query);
                $query->execute();
                $count = $query->fetch();
                
                // @codeCoverageIgnoreStart
            } catch (Exception $e) {
                throw new DaoException($e->getMessage(), $e->getCode(), $e);
            }
            // @codeCoverageIgnoreEnd

            $name = $datapoint->settings->name;
           
            $result[$name.""] = $count[0];
           
        }
        
        return $result;
    }

}