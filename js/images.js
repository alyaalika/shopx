function _typeof(obj) {
    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
        _typeof = function _typeof(obj) { return typeof obj; };
    } else {
        _typeof = function _typeof(obj) {
            return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
        };
    }
    return _typeof(obj);
}

(function (_, $) {
    (function ($) {
        function _getTabTools(id) {
            return $('.cm-tab-tools[id^="tools_' + id + '"]');
        }

        function _getTabIds(id) {
            var result_ids = ['content_' + id];
            var additional_ids = $('#content_' + id).data('caTabTargetId');

            if (additional_ids) {
                result_ids.push(additional_ids);
            }

            _getTabTools(id).each(function () {
                result_ids.push($(this).prop('id'));
            });

            return result_ids.join(',');
        }

        function _switchTab(tab_id, tabs_elm, show) {
            var id_obj = $('#content_' + tab_id);
            var tab_tools = _getTabTools(tab_id);

            if (show) {
                id_obj.removeClass('hidden');
                tab_tools.removeClass('hidden');

                if (id_obj.hasClass('cm-hide-save-button')) {
                    $('.cm-save-buttons').addClass('hidden');
                } else {
                    $('.cm-save-buttons').removeClass('hidden');
                }

                $.ceEvent('trigger', 'ce.tab.show', [tab_id, tabs_elm]);
            } else {
                id_obj.addClass('hidden');
                tab_tools.addClass('hidden');
            }

            return true;
        }

        function _cloneTools(tab_id, prev_id) {
            if (!tab_id || !prev_id) {
                return;
            }

            var _prev_tools = _getTabTools(prev_id);

            _prev_tools.each(function () {
                var self = $(this);
                var _new_id = self.prop('id').replace(prev_id, tab_id);

                if (!$('#' + _new_id).length) {
                    var _new_tool = self.clone();

                    _new_tool.children().remove();
                    _new_tool.prop('id', _new_id).addClass('hidden').appendTo(self.parent());
                }
            });
        }

        var methods = {
            switch: function _switch(tab_id) {
                if (!tab_id) {
                    return false;
                }

                var tabs_elm = $(this);
                var ul = tabs_elm.data('ceTabs').ul;
                var elm = tabs_elm.data('ceTabs').tabs[tab_id];

                if (!ul || !elm) {
                    return false;
                }

                var $tabs = $(tabs_elm);

                if ($tabs.hasClass('cm-track')) {
                    var input_name = 'selected_section';

                    if (typeof $tabs.data('caTabsInputName') !== 'undefined') {
                        input_name = $tabs.data('caTabsInputName') === '' ? 'selected_sub_section' : $tabs.data('caTabsInputName');
                    }

                    $("input[name=".concat(input_name, "]")).val(tab_id);
                }

                if (elm.hasClass('cm-js') == false) {
                    return false;
                }

                var active_id = $('li.active:first', ul).prop('id');
                var content_id = 'content_' + tab_id;
                $('li', ul).each(function () {
                    var self = $(this);
                    self.removeClass('active');

                    _switchTab(self.prop('id'), tabs_elm, false);
                });

                elm.addClass('active');
                var sub_tab = elm.parents('.cm-subtabs', ul);

                if (sub_tab.length && elm.hasClass('cm-no-highlight')) {
                    sub_tab.addClass('active');
                }

                if (elm.hasClass('cm-ajax-onclick') && !elm.hasClass('cm-ajax-onclick-active')) {
                    $.ceAjax('request', $('a', elm).prop('href'), {
                        result_ids: _getTabIds(tab_id),
                        callback: function callback(data) {
                            _switchTab(tab_id, tabs_elm, true);
                        }
                    });
                    elm.addClass('cm-ajax-onclick-active');
                    return true;
                }

                if (elm.hasClass('cm-ajax-onclick-active')) {
                    _switchTab(tab_id, tabs_elm, true);
                    return true;
                }

                if (elm.hasClass('cm-ajax') && $('#' + content_id).length == 0) {
                    tabs_elm.after('<div id="' + content_id + '"></div>');

                    _cloneTools(tab_id, active_id);

                    $.ceAjax('request', $('a', elm).prop('href'), {
                        result_ids: _getTabIds(tab_id),
                        callback: function callback(data) {
                            _switchTab(tab_id, tabs_elm, true);
                        }
                    });
                } else {
                    _switchTab(tab_id, tabs_elm, true);
                }

                return true;
            },
            init: function init() {
                $(this).each(function () {
                    var tabs_elm = $(this);
                    $.ceEvent('trigger', 'ce.tab.pre_init', [tabs_elm]);

                    var ul = $('ul:first', tabs_elm);
                    var list = $('li', ul);
                    var data = {
                        ul: ul,
                        tabs: {}
                    };
                    list.each(function () {
                        var elm = $(this);
                        var tab_id = elm.prop('id');
                        data.tabs[tab_id] = elm;
                    });
                    tabs_elm.data('ceTabs', data);
                    list.on('click', function (e) {
                        var $clickedElm = $(e.target);

                        if ($clickedElm.data('caStopEventPropagation')) {
                            e.stopPropagation();
                            return;
                        }

                        var elm = $(this);
                        var tab_id = elm.prop('id');

                        if (tabs_elm.ceTabs('switch', tab_id)) {
                            e.preventDefault();
                        }

                        return true;
                    });

                    var test;

                    if ((test = list.filter('.active')).length) {
                        test.trigger('click');
                    } else {
                        test = list.filter(':first').trigger('click');
                    }

                    var active_id = test.prop('id');
                    $('li.cm-ajax.cm-js').each(function () {
                        var self = $(this);
                        var tab_id = self.prop('id');

                        if (self.hasClass('active')) {
                            content = $('#content_' + tab_id).html().replace(/<!--.*?-->/, '').replace(/(^\s+|\s+$)/, '');

                            if (content.length) {
                                return true;
                            }
                        }

                        if (!self.data('passed') && $('a', self).prop('href')) {
                            self.data('passed', true);
                            var id = 'content_' + tab_id;
                            var block = $('#' + id);

                            if (!block.length) {
                                self.parents('.cm-j-tabs').eq(0).next().prepend('<div id="' + id + '"></div>');
                                block = $('#' + id);
                            }

                            if (!self.hasClass('active')) {
                                block.addClass('hidden');
                            }

                            _cloneTools(tab_id, active_id);

                            if (!self.hasClass('cm-ajax-onclick')) {
                                $.ceAjax('request', $('a', self).prop('href'), {
                                    result_ids: _getTabIds(tab_id),
                                    hidden: true,
                                    repeat_on_error: true
                                });
                            }
                        }
                    });
                    return true;
                });
                $.ceEvent('trigger', 'ce.tab.init');
            }
        };

        $.fn.ceTabs = function (method) {
            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (_typeof(method) === 'object' || !method) {
                return methods.init.apply(this, arguments);
            } else {
                $.error('ty.tabs: method ' + method + ' does not exist');
            }
        };
    })($);
})(Tygh, Tygh.$);
