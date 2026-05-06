/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(['uiComponent', 'ko', 'jquery'], function (Component, ko, $) {
    'use strict';

    return Component.extend({
        defaults: {
            graphqlUrl: ''
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

        graphql: function (query) {
            return $.ajax({
                url: this.graphqlUrl,
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                showLoader: true,
                data: JSON.stringify({query: query})
            });
        },

        loadEmployees: function () {
            var self = this;
            var query = `
                query {
                    adobeEmployees {
                        id
                        customer_id
                        name
                        joining_date
                        designation
                        address
                        status
                        hobbies
                    }
                }
            `;

            self.graphql(query).done(function (response) {
                var rows = response.data.adobeEmployees || [];
                rows.forEach(function (row) {
                    row.hobbies_text = row.hobbies ? row.hobbies.join(',') : '';
                });
                self.employees(rows);
            });
        },

        showAddForm: function () {
            this.clearForm();
            this.formTitle('Add Employee');
            this.isFormVisible(true);
        },

        editEmployee: function (employee) {
            this.employeeId(employee.id);
            this.name(employee.name);
            this.joiningDate(employee.joining_date);
            this.designation(employee.designation);
            this.address(employee.address);
            this.status(String(employee.status));
            this.hobbies(employee.hobbies || []);
            this.formTitle('Edit Employee');
            this.isFormVisible(true);
        },

        saveEmployee: function () {
            var self = this;
            var id = self.employeeId();

            var hobbies = self.hobbies().map(function (hobby) {
                return '"' + hobby + '"';
            }).join(',');

            var mutation;

            if (id) {
                mutation = `
                    mutation {
                        updateAdobeEmployee(input: {
                            id: ${id}
                            name: "${self.name()}"
                            joining_date: "${self.joiningDate()}"
                            designation: "${self.designation()}"
                            address: "${self.address()}"
                            status: ${parseInt(self.status(), 10)}
                            hobbies: [${hobbies}]
                        }) {
                            success
                            message
                        }
                    }
                `;
            } else {
                mutation = `
                    mutation {
                        createAdobeEmployee(input: {
                            name: "${self.name()}"
                            joining_date: "${self.joiningDate()}"
                            designation: "${self.designation()}"
                            address: "${self.address()}"
                            status: ${parseInt(self.status(), 10)}
                            hobbies: [${hobbies}]
                        }) {
                            success
                            message
                        }
                    }
                `;
            }

            self.graphql(mutation).done(function () {
                self.clearForm();
                self.isFormVisible(false);
                self.loadEmployees();
            });

            return false;
        },

        deleteEmployee: function (employee) {
            var self = this;

            if (!confirm('Are you sure you want to delete this employee?')) {
                return;
            }

            var mutation = `
                mutation {
                    deleteAdobeEmployee(id: ${employee.id}) {
                        success
                        message
                    }
                }
            `;

            self.graphql(mutation).done(function () {
                self.loadEmployees();
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