/**
 * Created by Neo on 2017/6/15.
 */


import Vue from 'vue';
import shop_diy_index from './components/shop_diy_index.vue';


let app = new Vue({
    el: '#application',
    component:{
        shop_diy_index
    },
    render: h => h(shop_diy_index)
});
