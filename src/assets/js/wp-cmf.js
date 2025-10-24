/**
 * WP-CMF Core Fields JavaScript
 *
 * Default scripts for WP-CMF field types
 * Provides field validation, interactions, and enhancements
 *
 * @package Pedalcms\WpCmf
 * @since 1.0.0
 */

(function($) {
	'use strict';

	/**
	 * WP-CMF Fields Object
	 */
	const WpCmfFields = {

		/**
		 * Initialize all field functionality
		 */
		init: function() {
			this.initColorPicker();
			this.initValidation();
			this.initCharacterCounter();
			this.initNumberFields();
			this.initConditionalFields();
			this.initSelectAll();
		},

		/**
		 * Initialize WordPress Color Picker for color fields
		 */
		initColorPicker: function() {
			if (typeof $.fn.wpColorPicker !== 'undefined') {
				$('.wp-cmf-field input[type="color"].use-wp-picker').each(function() {
					$(this).wpColorPicker({
						change: function(event, ui) {
							$(this).trigger('change');
						}
					});
				});
			}
		},

		/**
		 * Initialize client-side validation
		 */
		initValidation: function() {
			// Email validation
			$('.wp-cmf-field input[type="email"]').on('blur', function() {
				const email = $(this).val();
				const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

				if (email && !emailRegex.test(email)) {
					WpCmfFields.showError($(this), 'Please enter a valid email address.');
				} else {
					WpCmfFields.clearError($(this));
				}
			});

			// URL validation
			$('.wp-cmf-field input[type="url"]').on('blur', function() {
				const url = $(this).val();
				const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;

				if (url && !urlRegex.test(url)) {
					WpCmfFields.showError($(this), 'Please enter a valid URL.');
				} else {
					WpCmfFields.clearError($(this));
				}
			});

			// Required field validation on form submit
			$('form').on('submit', function(e) {
				let hasErrors = false;

				$(this).find('.wp-cmf-field [required]').each(function() {
					const $field = $(this);
					const fieldType = $field.attr('type');
					let isEmpty = false;

					if (fieldType === 'checkbox' || fieldType === 'radio') {
						const name = $field.attr('name');
						isEmpty = !$('input[name="' + name + '"]:checked').length;
					} else {
						isEmpty = !$field.val() || $field.val().trim() === '';
					}

					if (isEmpty) {
						WpCmfFields.showError($field, 'This field is required.');
						hasErrors = true;
					}
				});

				if (hasErrors) {
					e.preventDefault();
					// Scroll to first error
					$('html, body').animate({
						scrollTop: $('.wp-cmf-field.has-error:first').offset().top - 100
					}, 300);
				}
			});
		},

		/**
		 * Show validation error
		 */
		showError: function($field, message) {
			const $wrapper = $field.closest('.wp-cmf-field');
			$wrapper.addClass('has-error');

			// Remove existing error message
			$wrapper.find('.wp-cmf-field-error').remove();

			// Add new error message
			$field.after('<span class="wp-cmf-field-error">' + message + '</span>');
		},

		/**
		 * Clear validation error
		 */
		clearError: function($field) {
			const $wrapper = $field.closest('.wp-cmf-field');
			$wrapper.removeClass('has-error');
			$wrapper.find('.wp-cmf-field-error').remove();
		},

		/**
		 * Initialize character counter for text fields with maxlength
		 */
		initCharacterCounter: function() {
			$('.wp-cmf-field input[maxlength], .wp-cmf-field textarea[maxlength]').each(function() {
				const $field = $(this);
				const maxLength = $field.attr('maxlength');
				const $counter = $('<span class="wp-cmf-char-counter" style="display:block;margin-top:5px;color:#646970;font-size:12px;"></span>');

				$field.after($counter);

				const updateCounter = function() {
					const currentLength = $field.val().length;
					$counter.text(currentLength + ' / ' + maxLength + ' characters');

					if (currentLength >= maxLength) {
						$counter.css('color', '#d63638');
					} else {
						$counter.css('color', '#646970');
					}
				};

				updateCounter();
				$field.on('input', updateCounter);
			});
		},

		/**
		 * Enhance number fields
		 */
		initNumberFields: function() {
			// Add step buttons for number fields (optional enhancement)
			$('.wp-cmf-field input[type="number"]').each(function() {
				const $field = $(this);
				const min = parseFloat($field.attr('min')) || null;
				const max = parseFloat($field.attr('max')) || null;

				// Validate on change
				$field.on('change', function() {
					let value = parseFloat($(this).val());

					if (isNaN(value)) {
						return;
					}

					if (min !== null && value < min) {
						$(this).val(min);
						WpCmfFields.showError($(this), 'Value must be at least ' + min);
					} else if (max !== null && value > max) {
						$(this).val(max);
						WpCmfFields.showError($(this), 'Value must be at most ' + max);
					} else {
						WpCmfFields.clearError($(this));
					}
				});
			});
		},

		/**
		 * Initialize conditional field visibility
		 * Based on data-show-if attributes (for future enhancement)
		 */
		initConditionalFields: function() {
			$('[data-show-if]').each(function() {
				const $field = $(this);
				const showIfData = $field.data('show-if');

				if (typeof showIfData === 'object') {
					const targetField = showIfData.field;
					const targetValue = showIfData.value;
					const $targetField = $('[name="' + targetField + '"]');

					const checkVisibility = function() {
						const currentValue = $targetField.val();
						if (currentValue == targetValue) {
							$field.show();
						} else {
							$field.hide();
						}
					};

					checkVisibility();
					$targetField.on('change', checkVisibility);
				}
			});
		},

		/**
		 * Add "Select All" / "Deselect All" for checkbox groups
		 */
		initSelectAll: function() {
			$('.wp-cmf-field-checkbox-group').each(function() {
				const $group = $(this);
				const $checkboxes = $group.find('input[type="checkbox"]');

				if ($checkboxes.length > 3) {
					const $selectAll = $('<button type="button" class="button button-small" style="margin-bottom:10px;width:100px;display:inline-block;">Select All</button>');
					$group.prepend($selectAll);

					$selectAll.on('click', function() {
						const allChecked = $checkboxes.filter(':checked').length === $checkboxes.length;

						if (allChecked) {
							$checkboxes.prop('checked', false);
							$(this).text('Select All');
						} else {
							$checkboxes.prop('checked', true);
							$(this).text('Deselect All');
						}
					});

					// Update button text on checkbox change
					$checkboxes.on('change', function() {
						const allChecked = $checkboxes.filter(':checked').length === $checkboxes.length;
						$selectAll.text(allChecked ? 'Deselect All' : 'Select All');
					});
				}
			});
		},

		/**
		 * Utility: Debounce function
		 */
		debounce: function(func, wait) {
			let timeout;
			return function executedFunction(...args) {
				const later = () => {
					clearTimeout(timeout);
					func(...args);
				};
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
			};
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		WpCmfFields.init();
	});

	/**
	 * Reinitialize after AJAX (for dynamic field additions)
	 */
	$(document).on('wp-cmf-fields-added', function() {
		WpCmfFields.init();
	});

	// Expose to global scope for external access
	window.WpCmfFields = WpCmfFields;

})(jQuery);
