function fn_change_options(objId, id, optionId) {
  var $ = Tygh.$;
  var cart_changed = true;
  var self = $(':input[id*=_' + objId + '_' + optionId + ']').last()[0];
  var formData = [];
  var varNames = [];
  var updateIds = [];
  var cacheQuery = true;
  var defaultValues = {};
  var parents = $('.cm-reload-' + objId);

  parents.each(function (index, parentElm) {
      var reloadId = $(parentElm).prop('id');
      updateIds.push(reloadId);
      defaultValues[reloadId] = {};
      var elms = $(':input:not([type=radio]):not([type=checkbox])', parentElm);

      elms.each(function (index, elm) {
          if ($(elm).prop('disabled')) {
              return true;
          }

          if (elm.type !== 'submit' && elm.type !== 'file' && 
              !($(this).hasClass('cm-hint') && elm.value === elm.defaultValue) && elm.name.length !== 0) {
              if (elm.name === 'no_cache' && elm.value) {
                  cacheQuery = false;
              }

              formData.push({ name: elm.name, value: elm.value });
              varNames.push(elm.name);
          }
      });

      elms = $(':input', parentElm);
      elms.each(function (index, elm) {
          var elmId = $(elm).prop('id');
          if (!elmId) {
              return true;
          }

          if ($(elm).is('select')) {
              $('option', elm).each(function () {
                  if (this.defaultSelected) {
                      defaultValues[reloadId][elmId] = this.value;
                  }
              });
          } else if ($(elm).is('input[type=radio], input[type=checkbox]')) {
              defaultValues[reloadId][elmId] = elm.defaultChecked;
          } else if (!$(elm).is('input[type=file]')) {
              defaultValues[reloadId][elmId] = elm.defaultValue;
          }
      });
  });

  var radio = $('input[type=radio]:checked, input[type=checkbox]', parents);
  radio.each(function (index, elm) {
      if ($(elm).prop('disabled')) {
          return true;
      }

      var value = elm.value;
      if ($(elm).is('input[type=checkbox]:checked')) {
          if (!$(elm).hasClass('cm-no-change')) {
              value = $(elm).val();
          }
      } else if ($(elm).is('input[type=checkbox]')) {
          if ($.inArray(elm.name, varNames) !== -1) {
              return true;
          }

          if (!$(elm).hasClass('cm-no-change')) {
              value = 'unchecked';
          } else {
              value = '';
          }
      }

      formData.push({ name: elm.name, value: value });
  });

  $.ceEvent('trigger', 'ce.product_option_changed', [objId, id, optionId, updateIds, formData]);
  var url = fn_url((Tygh.area === 'A' ? 'order_management.options?changed_option[' + id + ']=' + optionId : 'products.options?changed_option[' + id + ']=' + optionId));

  if (Tygh.area === 'A') {
      cacheQuery = false;
  }

  formData.forEach(function (data) {
      url += '&' + data.name + '=' + encodeURIComponent(data.value);
  });

  $.ceAjax('request', url, {
      result_ids: updateIds.join(','),
      caching: cacheQuery,
      force_exec: true,
      pre_processing: fn_pre_process_form_files,
      callback: function (data, params) {
          fn_post_process_form_files(data, params);
          var parents = $('.cm-reload-' + objId);
          parents.each(function (index, parentElm) {
              if (data.html && data.html[$(parentElm).prop('id')]) {
                  var reloadId = $(parentElm).prop('id');
                  var elms = $(':input', parentElm);
                  var checkedElms = [];

                  if (defaultValues[reloadId]) {
                      elms.each(function (index, elm) {
                          var elmId = $(elm).prop('id');
                          if (elmId && defaultValues[reloadId][elmId] != null) {
                              if ($(elm).is('select')) {
                                  var selected = {};
                                  var isSelected = false;
                                  $('option', elm).each(function () {
                                      selected[this.value] = this.defaultSelected;
                                      this.defaultSelected = (defaultValues[reloadId][elmId] == this.value);
                                  });
                                  $('option', elm).each(function () {
                                      this.selected = selected[this.value];
                                      if (this.selected) {
                                          isSelected = true;
                                      }
                                  });

                                  if (!isSelected) {
                                      $('option', elm).get(0).selected = true;
                                  }
                              } else if ($(elm).is('input[type=radio], input[type=checkbox]')) {
                                  var checked = elm.defaultChecked;
                                  if (checked) {
                                      checkedElms.push(elm);
                                  }
                                  elm.defaultChecked = defaultValues[reloadId][elmId];
                                  elm.checked = checked;
                              } else {
                                  var value = elm.defaultValue;
                                  elm.defaultValue = defaultValues[reloadId][elmId];
                                  elm.value = value;
                              }
                          }
                      });
                      $(checkedElms).prop('checked', true);
                  }
              }
          });

          for (var notificationKey in data.notifications) {
              if (data.notifications.hasOwnProperty(notificationKey)) {
                  var notify = data.notifications[notificationKey];
                  if (notify.extra == 'zero_inventory') {
                      return;
                  }
              }
          }

          $.ceEvent('trigger', 'ce.product_option_changed_post', [objId, id, optionId, updateIds, formData, data, params, self]);
      },
      method: 'post'
  });
}

function fn_set_option_value(id, optionId, value) {
  var $ = Tygh.$;
  var elm = $('#option_' + id + '_' + optionId);

  if (elm.prop('disabled')) {
      return false;
  }

  if (elm.prop('type') == 'select-one') {
      elm.val(value).change();
  } else {
      var elms = $('#option_' + id + '_' + optionId + '_group');
      if ($.browser.msie) {
          $('input[type=radio][value=' + value + ']', elms).prop('checked', true);
      }
      $('input[type=radio][value=' + value + ']', elms).click();
  }

  return true;
}

function fn_pre_process_form_files(data, params) {
  var $ = Tygh.$;

  if (data.html) {
      $(Tygh.body).append('<div id="file_container" class="hidden"></div>');
      var container = $('#file_container');

      for (var k in data.html) {
          $('#' + k + ' .fileuploader, #' + k + ' .ty-fileuploader').each(function (index, elm) {
              var jelm = $(elm);
              var jparent = jelm.parents('.control-group, .ty-control-group');
              jparent.appendTo(container);
              jparent.prop('id', 'moved_' + jparent.prop('id'));
          });
      }
  }
}

function fn_post_process_form_files(data, params) {
  var $ = Tygh.$;
  var container = $('#file_container');

  $('div.control-group, div.ty-control-group', container).each(function (index, elm) {
      var jelm = $(elm);
      var elmId = jelm.prop('id').replace('moved_', '');
      var target = $('#' + elmId);
      target.html('');
      jelm.children().appendTo(target);
  });

  container.remove();
}

function fn_change_variant_image(prefix, optId, varId) {
  var $ = Tygh.$;
  var self = this;
  $('[id*=variant_image_' + prefix + '_' + optId + ']').removeClass('product-variant-image-selected').addClass('product-variant-image-unselected');

  if (typeof varId === 'undefined') {
      self = $('select[id*=_' + prefix + '_' + optId + ']')[0];
      varId = $(self).val();
  }

  if (typeof varId === 'undefined') {
      var $uncheckedVariant = $('#unchecked_option_' + prefix + '_' + optId);
      var $checkedVariant = $('#option_' + prefix + '_' + optId);

      if ($checkedVariant.length && $checkedVariant.is(':checked')) {
          self = $checkedVariant[0];
          varId = $checkedVariant.val();
      } else if ($uncheckedVariant.length) {
          self = $uncheckedVariant[0];
          varId = $uncheckedVariant.val();
      }
  }

  $('[id*=variant_image_' + prefix + '_' + optId + '_' + varId + ']').removeClass('product-variant-image-unselected').addClass('product-variant-image-selected');
  var formData = [];
  var varNames = [];
  var parents = $('.cm-reload-' + prefix);

  parents.each(function (index, parent_elm) {
      var elms = $(':input:not([type=radio]):not([type=checkbox])', parent_elm);
      elms.each(function (index, elm) {
          if (elm.type !== 'submit' && elm.type !== 'file' && 
              !($(this).hasClass('cm-hint') && elm.value === elm.defaultValue) && elm.name.length !== 0) {
              formData.push({ name: elm.name, value: elm.value });
              varNames.push(elm.name);
          }
      });
  });

  var selectables = $('input[type=radio]:checked, input[type=checkbox]', parents);
  selectables.each(function (index, elm) {
      if ($(elm).prop('disabled')) {
          return true;
      }

      var value = elm.value;
      if ($(elm).is('input[type=checkbox]:checked')) {
          if (!$(elm).hasClass('cm-no-change')) {
              value = $(elm).val();
          }
      } else if ($(elm).is('input[type=checkbox]')) {
          if ($.inArray(elm.name, varNames) !== -1) {
              return true;
          }

          if (!$(elm).hasClass('cm-no-change')) {
              value = 'unchecked';
          } else {
              value = '';
          }
      }

      formData.push({ name: elm.name, value: value });
  });

  $.ceEvent('trigger', 'ce.product_option_changed_post', [prefix, varId, optId, [], formData, {}, {}, self]);
}
