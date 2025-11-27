<template>
    <div>
        <div class="content p-4">
            <b-tabs>
                <b-tab title="Overview">
                    <analysis-component></analysis-component>
                </b-tab>
                <b-tab title="User Activity">
                    <user-activity-component></user-activity-component>
                </b-tab>
                <b-tab title="Page Analytics">
                    <files-statistic-component></files-statistic-component>
                </b-tab>
            </b-tabs>
        </div>
    </div>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
    import AnalysisComponent from './AnalysisComponent'
    import UserActivityComponent from './UserActivityComponent'
    import FilesStatisticComponent from './FilesStatisticComponent'

    export default {
        components: {
            AnalysisComponent,
            UserActivityComponent,
            FilesStatisticComponent
        },
    }
</script>

<style scoped>
>>> *,*::before,*::after{box-sizing:border-box;}
>>> [tabindex="-1"]:focus:not(:focus-visible){outline:0!important;}
>>> ul{margin-top:0;margin-bottom:1rem;}
>>> a{color:#007bff;text-decoration:none;background-color:transparent;}
>>> a:hover{color:#0056b3;text-decoration:underline;}
>>> .nav{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;padding-left:0;margin-bottom:0;list-style:none;}
>>> .nav-link{display:block;padding:0.5rem 1rem;}
>>> .nav-link:hover,.nav-link:focus{text-decoration:none;}
>>> .nav-tabs{border-bottom:1px solid #dee2e6;}
>>> .nav-tabs .nav-item{margin-bottom:-1px;}
>>> .nav-tabs .nav-link{border:1px solid transparent;border-top-left-radius:0.25rem;border-top-right-radius:0.25rem;}
>>> .nav-tabs .nav-link:hover,.nav-tabs .nav-link:focus{border-color:#e9ecef #e9ecef #dee2e6;}
>>> .nav-tabs .nav-link.active{color:#495057;background-color:#fff;border-color:#dee2e6 #dee2e6 #fff;}
>>> .tab-content > .tab-pane{display:none;}
>>> .tab-content > .active{display:block;}
@media print{
>>> *,*::before,*::after{text-shadow:none!important;box-shadow:none!important;}
>>> a:not(.btn){text-decoration:underline;}
}
>>> a{cursor:pointer;}
>>> a{-moz-osx-font-smoothing:grayscale;-webkit-font-smoothing:antialiased;font-family:"Muli", "Helvetica", Arial, sans-serif;}
>>> a{color:#68B3C8;}
>>> a:hover,a:focus{color:#429cb6;text-decoration:none;}
>>> a:focus,a:active{outline:0!important;}
>>> .nav-tabs{border-bottom:none;}
>>> .nav-tabs:not(.wizard-tabs){border-bottom:0 none;font-size:16px;font-weight:600;}
>>> .nav-tabs:not(.wizard-tabs) > li > a{color:#A49E93;}
>>> .nav-tabs:not(.wizard-tabs) > li > a:hover{color:#66615b;}
>>> .tabs:not(.row) .tab-content{border-top:1px solid #dee2e6;padding-top:1em;}
>>> .tab-content > .active{outline:none;}
>>> .nav > li a{position:relative;}
>>> .nav > li > a:hover,.nav > li > a:focus{background-color:transparent;}
>>> @font-face{font-family:'Muli';font-style:normal;font-weight:300;src:url(https://fonts.gstatic.com/s/muli/v22/7Auwp_0qiz-afT3GLQjUwkQ1OQ.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
>>> @font-face{font-family:'Muli';font-style:normal;font-weight:300;src:url(https://fonts.gstatic.com/s/muli/v22/7Auwp_0qiz-afTzGLQjUwkQ1OQ.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
>>> @font-face{font-family:'Muli';font-style:normal;font-weight:300;src:url(https://fonts.gstatic.com/s/muli/v22/7Auwp_0qiz-afTLGLQjUwkQ.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
>>> @font-face{font-family:'Muli';font-style:normal;font-weight:400;src:url(https://fonts.gstatic.com/s/muli/v22/7Auwp_0qiz-afT3GLQjUwkQ1OQ.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
>>> @font-face{font-family:'Muli';font-style:normal;font-weight:400;src:url(https://fonts.gstatic.com/s/muli/v22/7Auwp_0qiz-afTzGLQjUwkQ1OQ.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
>>> @font-face{font-family:'Muli';font-style:normal;font-weight:400;src:url(https://fonts.gstatic.com/s/muli/v22/7Auwp_0qiz-afTLGLQjUwkQ.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
</style>
