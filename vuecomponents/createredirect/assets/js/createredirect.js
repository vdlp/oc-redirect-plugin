import WidgetBase from '../../../../../../../modules/dashboard/vuecomponents/dashboard/assets/js/widget-base.js';

export default {
    extends: WidgetBase,
    data: function () {
        return {
            from_url: '',
            to_url: '',
            from_url_error: false,
            to_url_error: false,
        }
    },
    methods: {
        useCustomData: function () {
            return true;
        },

        makeDefaultConfigAndData: function () {
            this.widget.configuration.title = this.widget.configuration.Title;
        },

        getSettingsConfiguration: function () {
            return [{
                property: "title",
                title: "Title",
                type: "string",
            }];
        },

        onClickSubmit: function () {
            this.from_url_error = this.from_url === '';
            this.to_url_error = this.to_url === '';

            if (!this.from_url_error && !this.to_url_error) {
                this.request('onSubmit', {
                    from_url: this.from_url,
                    to_url: this.to_url
                });
            }
        }
    }
};