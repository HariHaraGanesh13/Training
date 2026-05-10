define(['uiComponent', 'ko', 'jquery'], function (Component, ko, $) {
    'use strict';

    return Component.extend({
        defaults: {
            listUrl: '',
            getUrl: '',
            saveUrl: '',
            deleteUrl: ''
        },

        initialize: function () {
            this._super();

            this.employees = ko.observableArray([]);
            this.isFormVisible = ko.observable(false);
            this.formTitle = ko.observable('Add Employee');

            this.employeeId = ko.observable('');
            this.name = ko.observable('');
            this.joiningDate = ko.observable('');
            this.designation = ko.observable('');
            this.address = ko.observable('');
            this.status = ko.observable('1');
            this.hobbies = ko.observableArray([]);

            this.hobbyOptions = ko.observableArray([
                {value: 'Reading', label: 'Reading'},
                {value: 'Traveling', label: 'Traveling'},
                {value: 'Sports', label: 'Sports'},
                {value: 'Music', label: 'Music'}
            ]);

            this.loadEmployees();
            return this;
        },

        loadEmployees: function () {
            var self = this;

            $.ajax({
                url: self.listUrl,
                type: 'GET',
                dataType: 'json',
                showLoader: true
            }).done(function (response) {
                self.employees(response.items || []);
            });
        },

        showAddForm: function () {
            this.clearForm();
            this.formTitle('Add Employee');
            this.isFormVisible(true);
        },

        editEmployee: function (employee) {
            var self = this;

            $.ajax({
                url: self.getUrl,
                type: 'GET',
                dataType: 'json',
                showLoader: true,
                data: {
                    id: employee.id
                }
            }).done(function (response) {
                var item = response.item;

                self.employeeId(item.id);
                self.name(item.name);
                self.joiningDate(item.joining_date);
                self.designation(item.designation);
                self.address(item.address);
                self.status(String(item.status));
                self.hobbies(item.hobbies || []);
                self.formTitle('Edit Employee');
                self.isFormVisible(true);
            });
        },

        saveEmployee: function () {
            var self = this;

            $.ajax({
                url: self.saveUrl,
                type: 'POST',
                dataType: 'json',
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id: self.employeeId(),
                    name: self.name(),
                    joining_date: self.joiningDate(),
                    designation: self.designation(),
                    address: self.address(),
                    status: self.status(),
                    hobbies: self.hobbies()
                }
            }).done(function (response) {
                if (response.success) {
                    self.clearForm();
                    self.isFormVisible(false);
                    self.loadEmployees();
                } else {
                    alert(response.message || 'Unable to save employee.');
                }
            });

            return false;
        },

        deleteEmployee: function (employee) {
            var self = this;

            if (!confirm('Are you sure you want to delete this employee?')) {
                return;
            }

            $.ajax({
                url: self.deleteUrl,
                type: 'POST',
                dataType: 'json',
                showLoader: true,
                data: {
                    form_key: window.FORM_KEY,
                    id: employee.id
                }
            }).done(function (response) {
                if (response.success) {
                    self.loadEmployees();
                } else {
                    alert(response.message || 'Unable to delete employee.');
                }
            });
        },

        cancelForm: function () {
            this.clearForm();
            this.isFormVisible(false);
        },

        clearForm: function () {
            this.employeeId('');
            this.name('');
            this.joiningDate('');
            this.designation('');
            this.address('');
            this.status('1');
            this.hobbies([]);
            this.formTitle('Add Employee');
        }
    });
});