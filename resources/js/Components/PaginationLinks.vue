<template>
    <div v-if="paginationItems.total > paginationItems.per_page || showPaginationAllways" class="pt-6">
        <b-pagination
              v-if="!withLinks"
              v-model="paginationItems.current_page"
              :total-rows="paginationItems.total"
              :per-page="paginationItems.per_page"
              first-text="First"
              prev-text="Prev"
              next-text="Next"
              last-text="Last"
              align="center"
              @change="changePaginationPageMethod"
        >
        </b-pagination>

        <b-pagination-nav
            v-if="withLinks"
            v-model="paginationItems.current_page"
            :total-rows="paginationItems.total"
            :per-page="paginationItems.per_page"
            :pages="paginationLinks(paginationItems.links)"
            use-router
            router-component-name="router-link"
            first-text="First"
            prev-text="Prev"
            next-text="Next"
            last-text="Last"
            align="center"
        >
        </b-pagination-nav>
    </div>
</template>

<script>
    export default {
        props: ['withLinks', 'paginationItems', 'changePaginationPage', 'showPaginationAllways'],
        data() {
            return {
                mutatedCurrentPage: null
            }
        },
        methods: {
            changePaginationPageMethod(number) {
                console.log('change pagination method', this.changePaginationPage);
                if (this.changePaginationPage) {
                    this.changePaginationPage(number);
                }
            }
        },
        computed: {
            currentPageParam: {
                get: function () {
                  return this.mutatedCurrentPage
                },
                set: function (newValue) {
                  this.mutatedCurrentPage = this.currentPage
                }
            }
        }
    }
</script>

<style scoped>
/*! CSS Used from: http://localhost:8001/css/app.css */
@media (max-width: 575.98px){
.bv-d-xs-down-none{display:none!important;}
}
*,*::before,*::after{box-sizing:border-box;}
[tabindex="-1"]:focus:not(:focus-visible){outline:0!important;}
ul{margin-top:0;margin-bottom:1rem;}
button{margin:0;font-family:inherit;font-size:inherit;line-height:inherit;}
button{overflow:visible;}
button{text-transform:none;}
button,[type="button"]{-webkit-appearance:button;}
button::-moz-focus-inner,[type="button"]::-moz-focus-inner{padding:0;border-style:none;}
*,*::before,*::after{box-sizing:inherit;}
.justify-content-center{justify-content:center!important;}
ul{list-style:none;margin:0;padding:0;}
button{font-family:inherit;font-size:100%;line-height:1.15;margin:0;}
button{overflow:visible;}
button{text-transform:none;}
button,[type="button"]{-webkit-appearance:button;}
button::-moz-focus-inner,[type="button"]::-moz-focus-inner{border-style:none;padding:0;}
button:-moz-focusring,[type="button"]:-moz-focusring{outline:1px dotted ButtonText;}
button{background-color:transparent;background-image:none;}
button:focus{outline:1px dotted;outline:5px auto -webkit-focus-ring-color;}
ul{list-style:none;margin:0;padding:0;}
*,::before,::after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#d2d6dc;}
button{cursor:pointer;}
button{padding:0;line-height:inherit;color:inherit;}
/*! CSS Used from: Embedded */
*,*::before,*::after{box-sizing:border-box;}
[tabindex="-1"]:focus:not(:focus-visible){outline:0!important;}
@media print{
*,*::before,*::after{text-shadow:none!important;box-shadow:none!important;}
}
button{-webkit-appearance:button;}
button::-moz-focus-inner{padding:0;border-style:none;}
/*! CSS Used from: Embedded */
[data-v-1f1cfbdf] *,[data-v-1f1cfbdf]:after,[data-v-1f1cfbdf]:before{box-sizing:border-box;}
[data-v-1f1cfbdf] [tabindex="-1"]:focus:not(.focus-visible){outline:0!important;}
[data-v-1f1cfbdf] ul{margin-bottom:1rem;}
[data-v-1f1cfbdf] ul{margin-top:0;}
[data-v-1f1cfbdf] button{border-radius:0;}
[data-v-1f1cfbdf] button:focus{outline:1px dotted;outline:5px auto -webkit-focus-ring-color;}
[data-v-1f1cfbdf] button{margin:0;font-family:inherit;font-size:inherit;line-height:inherit;}
[data-v-1f1cfbdf] button{overflow:visible;}
[data-v-1f1cfbdf] button{text-transform:none;}
[data-v-1f1cfbdf] [type=button]{-webkit-appearance:button;}
[data-v-1f1cfbdf] [type=button]::-moz-focus-inner{padding:0;border-style:none;}
[data-v-1f1cfbdf] .pagination{display:flex;padding-left:0;list-style:none;border-radius:.25rem;}
[data-v-1f1cfbdf] .page-link{position:relative;display:block;padding:.5rem .75rem;margin-left:-1px;line-height:1.25;color:#007bff;background-color:#fff;border:1px solid #dee2e6;}
[data-v-1f1cfbdf] .page-link:hover{z-index:2;color:#0056b3;text-decoration:none;background-color:#e9ecef;border-color:#dee2e6;}
[data-v-1f1cfbdf] .page-link:focus{z-index:3;outline:0;box-shadow:0 0 0 .2rem rgba(0,123,255,.25);}
[data-v-1f1cfbdf] .page-item:first-child .page-link{margin-left:0;border-top-left-radius:.25rem;border-bottom-left-radius:.25rem;}
[data-v-1f1cfbdf] .page-item:last-child .page-link{border-top-right-radius:.25rem;border-bottom-right-radius:.25rem;}
[data-v-1f1cfbdf] .page-item.active .page-link{z-index:3;color:#fff;background-color:#007bff;border-color:#007bff;}
[data-v-1f1cfbdf] .page-item.disabled .page-link{color:#6c757d;pointer-events:none;cursor:auto;background-color:#fff;border-color:#dee2e6;}
@media print{
[data-v-1f1cfbdf] *,[data-v-1f1cfbdf]:after,[data-v-1f1cfbdf]:before{text-shadow:none!important;box-shadow:none!important;}
}
/*! CSS Used from: Embedded */
[data-v-3bd53768] *,*[data-v-3bd53768]::before,*[data-v-3bd53768]::after{box-sizing:border-box;}
[data-v-3bd53768] [tabindex="-1"]:focus:not(:focus-visible){outline:0!important;}
@media print{
[data-v-3bd53768] *,*[data-v-3bd53768]::before,*[data-v-3bd53768]::after{text-shadow:none!important;box-shadow:none!important;}
}
[data-v-3bd53768] *,[data-v-3bd53768]:after,[data-v-3bd53768]:before{box-sizing:border-box;}
[data-v-3bd53768] [tabindex="-1"]:focus:not(.focus-visible){outline:0!important;}
[data-v-3bd53768] ul{margin-bottom:1rem;}
[data-v-3bd53768] ul{margin-top:0;}
[data-v-3bd53768] button{border-radius:0;}
[data-v-3bd53768] button:focus{outline:1px dotted;outline:5px auto -webkit-focus-ring-color;}
[data-v-3bd53768] button{margin:0;font-family:inherit;font-size:inherit;line-height:inherit;}
[data-v-3bd53768] button{overflow:visible;}
[data-v-3bd53768] button{text-transform:none;}
[data-v-3bd53768] [type=button]{-webkit-appearance:button;}
[data-v-3bd53768] [type=button]::-moz-focus-inner{padding:0;border-style:none;}
[data-v-3bd53768] .pagination{display:flex;padding-left:0;list-style:none;border-radius:.25rem;}
[data-v-3bd53768] .page-link{position:relative;display:block;padding:.5rem .75rem;margin-left:-1px;line-height:1.25;color:#007bff;background-color:#fff;border:1px solid #dee2e6;}
[data-v-3bd53768] .page-link:hover{z-index:2;color:#0056b3;text-decoration:none;background-color:#e9ecef;border-color:#dee2e6;}
[data-v-3bd53768] .page-link:focus{z-index:3;outline:0;box-shadow:0 0 0 .2rem rgba(0,123,255,.25);}
[data-v-3bd53768] .page-item:first-child .page-link{margin-left:0;border-top-left-radius:.25rem;border-bottom-left-radius:.25rem;}
[data-v-3bd53768] .page-item:last-child .page-link{border-top-right-radius:.25rem;border-bottom-right-radius:.25rem;}
[data-v-3bd53768] .page-item.active .page-link{z-index:3;color:#fff;background-color:#007bff;border-color:#007bff;}
[data-v-3bd53768] .page-item.disabled .page-link{color:#6c757d;pointer-events:none;cursor:auto;background-color:#fff;border-color:#dee2e6;}
@media print{
[data-v-3bd53768] *,[data-v-3bd53768]:after,[data-v-3bd53768]:before{text-shadow:none!important;box-shadow:none!important;}
}
/*! CSS Used from: Embedded */
[data-v-4e7c81fd] *{box-sizing:border-box;}
[data-v-4e7c81fd] [tabindex="-1"]:focus:not(:focus-visible){outline:0!important;}
[data-v-4e7c81fd] ul{margin-top:0;margin-bottom:1rem;}
@media print{
[data-v-4e7c81fd] *{text-shadow:none!important;box-shadow:none!important;}
}

/*paginatation*/
/*! CSS Used from: https://bootstrap-vue.org/_nuxt/vendors/app.92f5f4f.css */
>>> *,:after,:before{box-sizing:border-box;}
>>> [tabindex="-1"]:focus:not(.focus-visible),[tabindex="-1"]:focus:not(:focus-visible){outline:0!important;}
>>> ul{margin-bottom:1rem;}
>>> ul{margin-top:0;}
>>> button{border-radius:0;}
>>> button:focus{outline:1px dotted;outline:5px auto -webkit-focus-ring-color;}
>>> button{margin:0;font-family:inherit;font-size:inherit;line-height:inherit;}
>>> button{overflow:visible;}
>>> button{text-transform:none;}
>>> [type=button],button{-webkit-appearance:button;}
>>> [type=button]::-moz-focus-inner,button::-moz-focus-inner{padding:0;border-style:none;}
>>> .pagination{display:flex;padding-left:0;list-style:none;border-radius:.25rem;}
>>> .page-link{position:relative;display:block;padding:.5rem .75rem;margin-left:-1px;line-height:1.25;color:#007bff;background-color:#fff;border:1px solid #dee2e6;}
>>> .page-link:hover{z-index:2;color:#0056b3;text-decoration:none;background-color:#e9ecef;border-color:#dee2e6;}
>>> .page-link:focus{z-index:3;outline:0;box-shadow:0 0 0 .2rem rgba(0,123,255,.25);}
>>> .page-item:first-child .page-link{margin-left:0;border-top-left-radius:.25rem;border-bottom-left-radius:.25rem;}
>>> .page-item:last-child .page-link{border-top-right-radius:.25rem;border-bottom-right-radius:.25rem;}
>>> .page-item.active .page-link{z-index:3;color:#fff;background-color:#007bff;border-color:#007bff;}
>>> .page-item.disabled .page-link{color:#6c757d;pointer-events:none;cursor:auto;background-color:#fff;border-color:#dee2e6;}
@media print{
>>> *,:after,:before{text-shadow:none!important;box-shadow:none!important;}
}
/*! CSS Used from: https://bootstrap-vue.org/_nuxt/app.846a6a3.css */
>>> .bd-example .pagination{margin-top:.5rem;margin-bottom:.5rem;}
</style>
