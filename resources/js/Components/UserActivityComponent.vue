<template>
    <div id="user-activity">
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div class="row" style="position: relative;">
                <div class="col-6">
                    <export-excel-button :filter_period="filter_period"
                                         :sortBy="sortBy"
                                         :sortDirection="sortDirection"
                    >
                    </export-excel-button>
                </div>
                <div class="col-6">
                    <b-form-group label="View Stats Over">
                        <b-form-select v-model="filter_period" @change="currentPage = 1;getData()">
                            <option value="past_day">Past 24 hours</option>
                            <option value="last_3_days">Past 3 days</option>
                            <option value="last_week">Past week</option>
                            <option value="last_month">Past month</option>
                            <option value="3_month">Past 3 months</option>
                            <option value="half_year 6 months">Past 6 months</option>
                            <option value="year">Past 12 months</option>
                            <option selected="selected" value="all_time">All Time</option>
                        </b-form-select>
                    </b-form-group> 
                </div>
            </div>

            <b-table id="user-activity-table" :items="items" :fields="fields" @sort-changed="sortChanged">
            </b-table>

            <pagination-links :currentPage="currentPage" :rows="rows" :perPage="perPage" :changePaginationPage="changePaginationPage"></pagination-links>
        </div>
    </div>
</template>

<script>
    import ExportExcelButton from './DashboardComponents/ExportExcelButton'
    import PaginationLinks from './DashboardComponents/PaginationLinks'

    export default {
        components: {
            ExportExcelButton,
            PaginationLinks
        },
        created() {
            this.getData()
        },
        data() {
            return {
                filter_period: 'all_time',
                sortBy: '',
                sortDirection: '',
                currentPage: 1,
                rows: 1,
                perPage: 1,
                items: [],
                fields: [{
                        key: 'user_name',
                        sortable: true
                    },
                    {
                        key: 'date_accessed',
                        sortable: true
                    },
                    {
                        key: 'folder',
                        sortable: true
                    },
                    {
                        key: 'file_video_viewed',
                        label: 'File/Video Viewed',
                        sortable: true
                    },
                ]
            }
        },
        methods: {
            getData() {
                axios.get(
                    this.route('api.admin.activity.user-activity-data',
                    {
                        filterDate: this.filter_period,
                        sortBy: this.sortBy,
                        sortDirection: this.sortDirection,
                        pageNumber: this.currentPage
                    }
                )
                  ).then(response => {
                    this.items = response.data.data;
                    this.currentPage = response.data.current_page
                    this.perPage = response.data.per_page
                    this.rows = response.data.total
                  });
            },
            sortChanged(e) {
                this.currentPage = 1;
                console.log('eeee', e)
                this.sortBy = e.sortBy;
                this.sortDirection = e.sortDesc ? 'DESC' : 'ASC';
                this.getData();
            },
            changePaginationPage(number) {
                this.currentPage = number;
                this.getData();
            }
        }
    }
</script>

<style scoped>
    >>> *,*::before,*::after{box-sizing:border-box;}
    >>> [tabindex="-1"]:focus:not(:focus-visible){outline:0!important;}
    >>> table{border-collapse:collapse;}
    >>> th{text-align:inherit;text-align:-webkit-match-parent;}
    >>> select{margin:0;font-family:inherit;font-size:inherit;line-height:inherit;}
    >>> select{text-transform:none;}
    >>> select{word-wrap:normal;}
    >>> fieldset{min-width:0;padding:0;margin:0;border:0;}
    >>> legend{display:block;width:100%;max-width:100%;padding:0;margin-bottom:.5rem;font-size:1.5rem;line-height:inherit;color:inherit;white-space:normal;}
    >>> .col-12,.col-md-9{position:relative;width:100%;padding-right:15px;padding-left:15px;}
    >>> .col-12{flex:0 0 100%;max-width:100%;}
    >>> .order-1{order:1;}
    @media (min-width: 768px){
    >>> .col-md-9{flex:0 0 75%;max-width:75%;}
    }
    >>> .table{width:100%;margin-bottom:1rem;color:#212529;}
    >>> .table th,.table td{padding:0.75rem;vertical-align:top;border-top:1px solid #dee2e6;}
    >>> .table thead th{vertical-align:bottom;border-bottom:2px solid #dee2e6;}
    >>> .col-form-label{padding-top:calc(0.375rem + 1px);padding-bottom:calc(0.375rem + 1px);margin-bottom:0;font-size:inherit;line-height:1.5;}
    >>> .form-group{margin-bottom:1rem;}
    >>> .custom-select{display:inline-block;width:100%;height:calc(1.5em + 0.75rem + 2px);padding:0.375rem 1.75rem 0.375rem 0.75rem;font-size:1rem;font-weight:400;line-height:1.5;color:#495057;vertical-align:middle;background:#fff url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='4' height='5' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e) no-repeat right 0.75rem center/8px 10px;border:1px solid >>> #ced4da;border-radius:0.25rem;-webkit-appearance:none;-moz-appearance:none;appearance:none;}
    .custom-select:focus{border-color:#80bdff;outline:0;box-shadow:0 0 0 0.2rem rgba(0, 123, 255, 0.25);}
    >>> .custom-select:disabled{color:#6c757d;background-color:#e9ecef;}
    >>> .custom-select::-ms-expand{display:none;}
    >>> .custom-select:-moz-focusring{color:transparent;text-shadow:0 0 0 #495057;}
    >>> .custom-select{transition:background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;}
    @media (prefers-reduced-motion: reduce){
    >>> .custom-select{transition:none;}
    }
    >>> .d-flex{display:flex!important;}
    >>> .justify-content-end{justify-content:flex-end!important;}
    >>> .pt-0{padding-top:0!important;}
    @media print{
    >>> *,*::before,*::after{text-shadow:none!important;box-shadow:none!important;}
    >>> thead{display:table-header-group;}
    >>> tr{page-break-inside:avoid;}
    >>> .table{border-collapse:collapse!important;}
    >>> .table td,.table th{background-color:#fff!important;}
    }
    /*! CSS Used from: Embedded */
    >>> .bv-no-focus-ring:focus{outline:none;}
    /*! CSS Used from: Embedded */
    >>> .table th,.table td{text-align:right;}
    >>> .table th:first-of-type,.table td:first-of-type{text-align:left;}
</style>