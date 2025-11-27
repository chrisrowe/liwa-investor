<template>
	<div style="position: absolute;top: 50%;transform: translateY(-50%);">
        <a :href="route(
        	'api.admin.activity.export-admin-dashboard-data',
        	{filterDate: filterPeriodParameter, sortBy: sortByParameter, sortDirection: sortDirectionParameter}
        )">
            <js-button>
                Export Excel
            </js-button>
        </a>
    </div>
</template>

<script>
    import JsButton from '../../Jetstream/Button'

	export default {
		components: {
			JsButton
		},
		props: ['filter_period', 'sortBy', 'sortDirection'],
		computed: {
			filterPeriodParameter() {
				return this.filter_period ? this.filter_period : 'all_time';
			},
			sortByParameter() {
				return this.sortBy ? this.sortBy : null;
			},
			sortDirectionParameter() {
				return this.sortDirection ? this.sortDirection : 'ASC';
			}
		}
	}
</script>