define(['marionette', 'text!layouts/categories/form.html', 'dispatcher', 'widgets/widget', 'views/extrafields/widget', 'views/image/widget'], function(Marionette, template, K2Dispatcher, K2Widget, K2ViewExtraFieldsWidget, K2ViewImageWidget) {'use strict';

	// K2 category form view
	var K2ViewCategory = Marionette.Layout.extend({

		// Template
		template : _.template(template),

		// Regions
		regions : {
			imageRegion : '#appCategoryImage',
			extraFieldsRegion : '#appCategoryExtraFields'
		},

		// UI events
		events : {
			'change #parent_id' : 'updateExtraFields'
		},

		// Model events
		modelEvents : {
			'change' : 'render'
		},

		// Initialize
		initialize : function() {

			// Add a listener for the before save event
			K2Dispatcher.on('app:controller:beforeSave', function() {
				this.onBeforeSave();
			}, this);

			// Image
			this.imageView = new K2ViewImageWidget({
				row : this.model,
				type : 'category'
			});

			// Extra fields
			this.extraFieldsView = new K2ViewExtraFieldsWidget({
				data : this.model.getForm().get('extraFields'),
				filterId : this.model.get('parent_id'),
				resourceId : this.model.get('id'),
				scope : 'category'
			});

		},

		// Serialize data for view
		serializeData : function() {
			var data = {
				'row' : this.model.toJSON(),
				'form' : this.model.getForm().toJSON()
			};
			return data;
		},

		onShow : function() {

			this.imageRegion.show(this.imageView);
			this.extraFieldsRegion.show(this.extraFieldsView);

		},

		// OnBeforeSave event
		onBeforeSave : function() {

			// Update form from editor contents
			K2Editor.save('description');
		},

		updateExtraFields : function(event) {
			event.preventDefault();
			this.extraFieldsView.trigger('filter', this.$el.find('#parent_id').val());
		},

		// OnDomRefresh event ( Marionette.js build in event )
		onDomRefresh : function() {

			// Initialize the editor
			K2Editor.init();

			// Restore Joomla! modal events
			if ( typeof (SqueezeBox) !== 'undefined') {
				SqueezeBox.initialize({});
				SqueezeBox.assign($$('a.modal-button'), {
					parse : 'rel'
				});
			}
			// Setup widgets
			K2Widget.updateEvents(this.$el);

		}
	});
	return K2ViewCategory;
});
