<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */


/**
 * Test class of Api/EmployeeService
 *
 * @group API
 */

use Orangehrm\Rest\Api\Pim\EmployeeSearchAPI;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeSearchAPITest extends PHPUnit_Framework_TestCase
{
    private $employeeSearchAPI;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeSearchAPI = new EmployeeSearchAPI($request);
    }

    public function testGetEmployeeDetails()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $parameterHolder = new \EmployeeSearchParameterHolder();
        $searchLimit = null;
        $searchOffset = null;

        $filters['employee_name'] = 'Nina';


        $parameterHolder->setFilters($filters);
        $parameterHolder->setLimit(0);
      //  $request->getActionRequest()->setParameter()
        $parameterHolder->setReturnType(\EmployeeSearchParameterHolder::RETURN_TYPE_OBJECT);

        $this->employeeSearchAPI = $this->getMock('Orangehrm\Rest\Api\Pim\EmployeeSearchAPI',array('buildSearchParamHolder'),array($request));
        $this->employeeSearchAPI->expects($this->once())
            ->method('buildSearchParamHolder')
            ->will($this->returnValue($parameterHolder));


        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Lewis');
        $employee->setFirstName('Nina');
        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId('001');
        $employeeList = new Doctrine_Collection('Employee');
        $employeeList[] = $employee;

        $pimEmployeeService = $this->getMock('EmployeeService');
        $pimEmployeeService->expects($this->any())
            ->method('searchEmployees')
            ->with($parameterHolder)
            ->will($this->returnValue($employeeList));

        $this->employeeSearchAPI->setEmployeeService($pimEmployeeService);
        $employeeReturned = $this->employeeSearchAPI->getEmployeeList();

        // creating the employee json array
        $apiEmployee = new Employee($employee->getFirstName(), $employee->getMiddleName(), $employee->getLastName(), 001);
        $apiEmployee->buildEmployee($employee);
        $jsonEmployeeArray = $apiEmployee->toArray();

        $assertResponse = new Response(array($jsonEmployeeArray), array());

        $this->assertEquals($assertResponse, $employeeReturned);

    }
}