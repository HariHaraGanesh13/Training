/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'ko',
    'jquery'
], function (Component, ko, $) {
    'use strict';

    return Component.extend({
        defaults: {
            employeeId: '',
            getUrl: '',
            saveUrl: '',
            backUrl: ''
        },

        initialize: function () {
            this._super();

            this.employeeId = parseInt(this.employeeId || 0, 10);

            this.pageTitle = ko.observable(this.employeeId > 0 ? 'Edit Employee' : 'Add Employee');

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

            if (this.employeeId > 0) {
                this.loadEmployee();
            }

            return this;
        },

        loadEmployee: function () {
            var self = this;

            $.ajax({
                url: self.getUrl,
                type: 'GET',
                dataType: 'json',
                showLoader: true,
                data: {
                    id: self.employeeId
                }
            }).done(function (response) {
                if (response.success) {
                    self.name(response.item.name);
                    self.joiningDate(response.item.joining_date);
                    self.designation(response.item.designation);
                    self.address(response.item.address);
                    self.status(String(response.item.status));
                    self.hobbies(response.item.hobbies || []);
                } else {
                    alert(response.message || 'Unable to load employee.');
                    window.location.href = self.backUrl;
                }
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
                    id: self.employeeId > 0 ? self.employeeId : '',
                    name: self.name(),
                    joining_date: self.joiningDate(),
                    designation: self.designation(),
                    address: self.address(),
                    status: self.status(),
                    hobbies: self.hobbies()
                }
            }).done(function (response) {
                if (response.success) {
                    window.location.href = self.backUrl;
                } else {
                    alert(response.message || 'Unable to save employee.');
                }
            });

            return false;
        },

        goBack: function () {
            window.location.href = this.backUrl;
        }
    });
});