<template>
    <div>
        <heading class="mb-6">Price  Tracker</heading>
        <div class="flex flex-row items-center justify-center">
            <div>
                <select name="currency" id="currency" v-model="currency" @change="_getData" 
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">--Select Currency--</option>
                    <option :value="curr" v-for="(curr, cindex) in currencies" :key="cindex">{{cindex}}</option>
                </select>
            </div>
            <div>
               <button @click="_getData" type="button" > Refresh  {{isLoading ? '...': ''}}</button>
            </div>
            <div>
                <select name="filter" id="filter" v-model="filter" @change="_getData" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">--Select filter--</option>
                    <option :value="findex" v-for="(filter, findex) in dateFilters" :key="findex">{{filter}}</option>
                </select>
           </div>
        </div>
        <br>

        <card
            class="bg-90 flex flex-col items-center justify-center"
            style="min-height: 300px"
        >
            <canvas id="myChart" width="800" height="600"></canvas>
        </card>
    </div>
</template>

<script>
import Chart from 'chart.js'


export default {

    mounted() {
        this._getData()
        this._getFilter()
    },

    data(){
        return{
            filter: '',
            currency: '',
            currencies: [],
            dateFilters: [],
            isLoading: false
        }
    },

    methods: {
        _renderTrend(lables, datasets){
            let ctx = document.getElementById('myChart').getContext('2d')
            new Chart( ctx, {
                type: 'line',
                data: {
                    labels: lables,
                    datasets: datasets
                },
                options: {
                    title: {
                        display: true,
                        text: 'INR Vs',
                        fontColor: 'white',
                        defaultFontStyle: 'bold',
                        fontSize: 16
                    },
                    legend: {
                        display: true,
                        labels: {
                            fontColor: 'white',
                            defaultFontStyle: 'bold',
                            fontSize: 14
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                fontColor: 'white'
                            },
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: 'white'
                            },
                        }]
                    }
                }
            })

        },

        async _getData(){
            try {
                if (this.isLoading) {
                    return
                }
                let filter = {
                    filter: this.filter,
                    currency: this.currency

                }
                this.isLoading = true
                let response = await Nova.request().post('/nova-vendor/price-tracker/history-price', filter)
                let lables = response.data.lables
                let datasets = response.data.datasets
                this._renderTrend(lables, datasets)
                this.isLoading =  false
            } catch (error) {
                console.log(error)
                this.isLoading =  false
            }
        },

        async _getFilter(){
            try {
                let response = await Nova.request().post('/nova-vendor/price-tracker/filters')
                let {currencies, filters} = response.data
                this.currencies = currencies
                this.dateFilters = filters
            } catch (error) {
                
            }
        }
    }
}
</script>

<style>
/* Scoped Styles */
</style>
