/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(['uiComponent', 'ko', 'jquery'], function (Component, ko, $) {
    'use strict';

    return Component.extend({
        defaults: {
            listUrl: '',
            baseUrl: ''
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
                if (Array.isArray(response)) {
                    self.employees(response[1] || []);
                    return;
                }

                self.employees(response.items || []);
            }).fail(function (xhr) {
                alert(xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'REST list API failed.');
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
                url: self.baseUrl + '/' + employee.id,
                type: 'GET',
                dataType: 'json',
                showLoader: true
            }).done(function (response) {
                var item = response.item || response;

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
            var id = self.employeeId();

            $.ajax({
                url: id ? self.baseUrl + '/' + id : self.baseUrl,
                type: id ? 'PUT' : 'POST',
                contentType: 'application/json',
                dataType: 'json',
                showLoader: true,
                data: JSON.stringify({
                    name: self.name(),
                    joiningDate: self.joiningDate(),
                    designation: self.designation(),
                    address: self.address(),
                    status: parseInt(self.status(), 10),
                    hobbies: self.hobbies()
                })
            }).done(function () {
                self.clearForm();
                self.isFormVisible(false);
                self.loadEmployees();
            }).fail(function (xhr) {
                alert(xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'REST save API failed.');
            });

            return false;
        },

        deleteEmployee: function (employee) {
            var self = this;

            if (!confirm('Are you sure you want to delete this employee?')) {
                return;
            }

            $.ajax({
                url: self.baseUrl + '/' + employee.id,
                type: 'DELETE',
                dataType: 'json',
                showLoader: true
            }).done(function () {
                self.loadEmployees();
            }).fail(function (xhr) {
                alert(xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'REST delete API failed.');
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