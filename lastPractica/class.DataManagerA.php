<?php

abstract class Party {
    protected $displayName;

    public function __construct($displayName) {
        $this->displayName = $displayName;
    }

    public function getDescription(array &$printedObjects = array(), $depth = 0) {
        $objectId = spl_object_id($this);

        if (in_array($objectId, $printedObjects, true)) {
            return;  // Evitar imprimir el mismo objeto otra vez
        }

        $printedObjects[] = $objectId;

        echo str_repeat("\t", $depth) . "Class: " . get_class($this) . PHP_EOL;
        echo str_repeat("\t", $depth) . "displayName: " . $this->displayName . PHP_EOL;

        $this->describe($printedObjects, $depth);
    }

    protected function describe(array &$printedObjects, $depth) {
        // Este método puede ser sobrescrito por las subclases
    }
}

class Person extends Party {
    private $firstName;
    private $lastName;
    private $orgUnits = array();

    public function __construct($displayName, $firstName, $lastName) {
        parent::__construct($displayName);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function addOrgUnit(OrgUnit $orgUnit) {
        $this->orgUnits[] = $orgUnit;
    }

    protected function describe(array &$printedObjects, $depth) {
        echo str_repeat("\t", $depth) . "firstName: " . $this->firstName . PHP_EOL;
        echo str_repeat("\t", $depth) . "lastName: " . $this->lastName . PHP_EOL;

        
    }
}

class OrgUnit extends Party {
    private $name;
    private $employees = array();
    private $company;

    public function __construct($displayName, $name) {
        parent::__construct($displayName);
        $this->name = $name;
    }

    public function setCompany(Company $company) {
        $this->company = $company;
    }

    public function addEmployee(Person $employee) {
        $this->employees[] = $employee;
        $employee->addOrgUnit($this);
    }

    protected function describe(array &$printedObjects, $depth) {
        echo str_repeat("\t", $depth) . "name: " . $this->name . PHP_EOL;

        if (!empty($this->employees)) {
            echo str_repeat("\t", $depth) . "Employees:" . PHP_EOL;
            foreach ($this->employees as $employee) {
                $employee->getDescription($printedObjects, $depth + 1);
            }
        }

        if ($this->company !== null) {
            echo str_repeat("\t", $depth) . "Company:" . PHP_EOL;
            $this->company->getDescription($printedObjects, $depth + 1);
        }
    }
}

class Company extends Party {
    private $units = array();

    public function __construct($displayName) {
        parent::__construct($displayName);
    }

    public function addUnit(OrgUnit $unit) {
        $this->units[] = $unit;
    }

    protected function describe(array &$printedObjects, $depth) {
        if (!empty($this->units)) {
            echo str_repeat("\t", $depth) . "Units:" . PHP_EOL;
            foreach ($this->units as $unit) {
                $unit->getDescription($printedObjects, $depth + 1);
            }
        }
    }
}

// Crear instancias
$company = new Company("MyCompany");

// Crear personas
$person1 = new Person("John Doe", "John", "Doe");
$person2 = new Person("Jane Smith", "Jane", "Smith");

// Crear OrgUnits
$orgUnit1 = new OrgUnit("OrgUnit1", "Org1");
$orgUnit2 = new OrgUnit("OrgUnit2", "Org2");

// Establecer relaciones corregidas
$person1->addOrgUnit($orgUnit1);
$person2->addOrgUnit($orgUnit2);

$orgUnit1->addEmployee($person1);
$orgUnit2->addEmployee($person2);

$company->addUnit($orgUnit1);
$company->addUnit($orgUnit2);

// Mostrar información
$company->getDescription();
?>
