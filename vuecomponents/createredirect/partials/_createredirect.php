<div class="widget-body">
    <h3 class="widget-title" v-text="widget.configuration.title"></h3>

    <div class="form-tabless-fields" v-if="fullWidgetData">
        <div class="form-group text-field span-left is-required">
            <label for="from_url" class="form-label" v-text="fullWidgetData.data.labels.from_url"></label>
            <input v-model="from_url" id="from_url" type="text" name="from_url" class="form-control" :placeholder="fullWidgetData.data.labels.from_url_placeholder" />
            <p class="form-text" v-text="fullWidgetData.data.labels.from_url_comment"></p>
            <span class="help-block text-danger" v-if="from_url_error" v-text="fullWidgetData.data.labels.from_url_error"></span>
        </div>
        <div class="form-group text-field span-right is-required">
            <label for="to_url" class="form-label" v-text="fullWidgetData.data.labels.to_url"></label>
            <input v-model="to_url" id="to_url" type="text" name="to_url" class="form-control" :placeholder="fullWidgetData.data.labels.to_url_placeholder" />
            <p class="form-text" v-text="fullWidgetData.data.labels.to_url_comment"></p>
            <span class="help-block text-danger" v-if="to_url_error" v-text="fullWidgetData.data.labels.to_url_error"></span>
        </div>
    </div>

    <button
        v-if="fullWidgetData"
        class="btn btn-primary"
        @click.stop.prevent="onClickSubmit"
    ><span v-text="fullWidgetData.data.labels.submit"></span></button>
</div>
