        $employees = [
            ['name' => 'John', 'city' => 'Dallas'],
            ['name' => 'Jane', 'city' => 'Austin'],
            ['name' => 'Jake', 'city' => 'Dallas'],
            ['name' => 'Jill', 'city' => 'Dallas'],
        ];

        $offices = [
            ['office' => 'Dallas HQ', 'city' => 'Dallas'],
            ['office' => 'Dallas South', 'city' => 'Dallas'],
            ['office' => 'Austin Branch', 'city' => 'Austin'],
        ];

        $result = [
            "Dallas" => [
                "Dallas HQ" => ["John", "Jake", "Jill"],
                "Dallas South" => ["John", "Jake", "Jill"],
            ],
            "Austin" => [
                "Austin Branch" => ["Jane"],
            ],
        ];

        // We need to create the collections with the arrays
        $employees = collect($employees);
        $offices = collect($offices);

        //then we will group the employees by city
        $employeesGroupedByCity = $employees->groupBy('city');

        //using the mapToGroups method we will group the employees by city and office see more at: https://laravel.com/docs/12.x/collections#method-maptogroups
        $groupedEmployeesByOffices = $offices->mapToGroups(function ($office) use ($employeesGroupedByCity) {
            $officeCity = $office['city'];
            $officeName = $office['office'];
            $employeesInCity = $employeesGroupedByCity->get($officeCity, collect())->pluck('name');
            return [$officeCity => [$officeName => $employeesInCity]];
        });

        dd($groupedEmployeesByOffices , $result);
