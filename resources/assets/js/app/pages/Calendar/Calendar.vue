<template>
    <article class="content">
        <v-spinner
            v-if="state.calendarLoading"
            :size="spinner_size"
            :speed="spinner_speed"
            :message="spinner_message"
        />
        <template v-else>
            <FullCalendar :options="calendarOptions" :events="EVENTS" />
        </template>
    </article>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'
    import FullCalendar from '@fullcalendar/vue'
    import dayGridPlugin from '@fullcalendar/daygrid'
    import timeGridPlugin from '@fullcalendar/timegrid'
    import interactionPlugin from '@fullcalendar/interaction'
    import ruLocale from '@fullcalendar/core/locales/ru'
    import moment from 'moment'

    export default {
        name: "Calendar",
        data() {
            return {
                spinner_size: 24,
                spinner_speed: 0.8,
                spinner_message: 'Загрузка',
                state: {
                    calendarLoading: true
                },
                eventsData: [],
            }
        },
        components: {
            FullCalendar
        },
        computed: {
            ...mapGetters(['EVENTS']),
             calendarOptions () {
                return {
                    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    initialView: 'dayGridMonth',
                    eventClick: this.handleEventClick,
                    locale: 'ruLocale',
                    firstDay: 1,
                    dayMaxEvents: true,
                    weekends: true,
                    buttonText: {
                        today: 'Сегодня',
                        month: 'Месяц',
                        week: 'Неделя',
                        day: 'День',
                        list: 'Список'
                    },
                    events: this.eventsData
                }
            }
        },
        methods: {
            ...mapActions([
                'getEvents',
            ]),
            handleEventClick(clickInfo) {
                alert(clickInfo.event.title);
            },
            async fetchEvents() {
                this.state.calendarLoading = true;
                await this.getEvents();
                this.state.calendarLoading = false;
            },
        },
        async mounted() {

            await this.fetchEvents();

            // Период сдачи показаний
            this.eventsData = [
                {
                    title: 'Период сдачи показаний',
                    start: this.EVENTS.meter_reading.start,
                    end: this.EVENTS.meter_reading.end,
                    color: '#ff9f89'
                },
            ];

            // for (let i = 0; i < 12; i++) {
            //
            //     // this.EVENTS.meter_reading.forEach((event) => {
            //     let start = moment(this.EVENTS.meter_reading.start).add(i, 'month').calendar();
            //     let end = moment(this.EVENTS.meter_reading.end).add(i, 'month').calendar();
            //         // start = start.setUTCMonth(i);
            //         // end = end.setUTCMonth(i);
            //     console.log(i, start, end);
            //         this.eventsData.push({
            //             title: 'Период сдачи показаний',
            //             start: start,
            //             end: end,
            //             color: '#ff9f89'
            //         });
            //     // });
            // }

            // Голосования
            this.EVENTS.polls.forEach((poll) => {
                this.eventsData.push({
                    id: poll.id,
                    title: 'Голосование: '+ poll.title,
                    description: poll.description,
                    start: poll.start,
                    end: poll.end
                });
            });

            // Заявки
            this.EVENTS.servicesRequest.forEach((servicesRequest) => {
                this.eventsData.push({
                    id: servicesRequest.id,
                    title: 'Заявка: '+ servicesRequest.title,
                    description: servicesRequest.description,
                    start: servicesRequest.execution_date,
                    end: servicesRequest.execution_date
                });
            });

        }
    }
</script>

<style>
    .fc-event-time {
        display : none;
    }
</style>
<style scoped>

</style>
