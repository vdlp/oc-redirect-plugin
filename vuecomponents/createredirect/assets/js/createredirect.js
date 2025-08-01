Vue.component('vdlp-redirect-vuecomponents-createredirect', {
    extends: Vue.options.components['dashboard-component-dashboard-widget-base'],
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
            Vue.set(this.widget.configuration, 'title', this.widget.configuration.Title);
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
    },
    template: '#vdlp_redirect_vuecomponents_createredirect'
});

