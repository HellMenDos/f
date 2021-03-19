<template>
    <article class="content">
        <v-spinner
                v-if="state.calendarLoading"
                :size="spinner_size"
                :speed="spinner_speed"
                :message="spinner_message"
        />
        <template v-else>
            <FullCalendar :options="calendarOptions" />
        </template>
    </article>
</template>

<script>
    import FullCalendar from '@fullcalendar/vue'
    import dayGridPlugin from '@fullcalendar/daygrid'
    import interactionPlugin from '@fullcalendar/interaction'

    export default {
        name: "Calendar",
        data() {
            return {
                spinner_size: 24,
                spinner_speed: 0.8,
                spinner_message: 'Загрузка',
                state: {
                    calendarLoading: true,
                },
                calendarOptions: {
                    plugins: [ dayGridPlugin, interactionPlugin ],
                    initialView: 'dayGridMonth',
                    dateClick: this.handleDateClick,
                    events: [
                        { title: 'event 1', date: '2019-04-01' },
                        { title: 'event 2', date: '2019-04-02' }
                    ]
                }
            }
        },
        components: {
            FullCalendar
        },
        methods: {
            handleDateClick: function(arg) {
                alert('date click! ' + arg.dateStr)
            }
        },
        mounted() {
            this.state.calendarLoading = false;
        }
    }
</script>

<style scoped>

</style>