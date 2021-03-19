<template>
	<article class="content">
		<header class="content_header">
			<h1 class="content_title">
				Заявки
				<a href="javascript:;"
				   @click="servicesRefresh()"
				   title="Обновить"
				   class="btn btn-success">
					<i class="fa fa-refresh"></i>
				</a>
			</h1>
		</header>
		<div class="content_body">
			<div class="row">
				<div class="col-md-12">
					<a href="javascript:;" @click="setShowModal" class="btn btn-warning btn-add-request">Добавить заявку</a>
					<v-modal
						v-if="modal == 'serviceModal'"
						@modalclose="modalClose"
					>
						<h2>{{ serviceModalHeader }}</h2>
						<ul class="nav nav-tabs" role="tablist" v-if="!isEditServiceRequest">
							<li class="nav-item">
								<a class="nav-link"
								   :class="[activeTab === 0 ? 'active' : '']"
								   @click="activateTab(0)"
								   href="javascript:;"
								   role="tab"
								>Выбрать</a>
							</li>
							<li class="nav-item">
								<a class="nav-link"
								   :class="[activeTab === 1 ? 'active' : '']"
								   @click="activateTab(1)"
								   href="javascript:;"
								   role="tab"
								>Добавить свою</a>
							</li>
						</ul>
						<br>
						<div class="tab-content">
							<div class="tab-pane fade" :class="[activeTab === 0 ? 'show active' : '']" role="tabpanel">
								<form @submit.prevent="onSubmitRequest" class="contact-form" method="post" accept-charset="utf-8">

									<div class="form-group">
										<div class="input-group">
											<p class="form-control" @click="selectService">{{ serviceTitle }}</p>
											<button class="btn btn-outline-secondary"
													@click="selectService"
													type="button"
													style="min-width: auto"
											>
												<i class="fa fa-list"></i>
											</button>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-4">
											<label for="quantity" class="control-label">Кол-во единиц услуги</label>
											<input type="number" id="quantity" class="form-control" v-model="quantity">
										</div>
										<div class="form-group col-8">
											<label class="control-label">Желаемая дата и время</label>
											<div class="input-group">
												<p class="form-control"
												   @click="selectExecutionDate"
												>{{ executionDateTitle }}</p>
												<button class="btn btn-outline-secondary"
														@click="selectExecutionDate"
														type="button"
														style="min-width: auto"
														:disabled="state.selectExecutionDateBtnDisabled"
														:title="state.selectExecutionDateBtnDisabled ? 'Выберите услугу!' : ''"
												>
													<i class="fa fa-calendar"></i>
												</button>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="requestText" class="control-label">Комментарий</label>
										<textarea
											id="requestText"
											v-model="requestText"
											rows="5"
											class="form-control"
											@blur="$v.$touch()"
										></textarea>
										<small
											class="text-danger"
											v-if="$v.requestText.$dirty && !$v.requestText.maxLength"
										>Поле должно быть не более {{$v.requestText.$params.maxLength.max}} символов</small>
									</div>
									<h4>Цена: {{ totalPrice }} руб.</h4>
									<input type="submit" class="btn btn-primary" name="submit" value="Отправить">
								</form>
							</div>
							<div class="tab-pane fade" :class="[activeTab === 1 ? 'show active' : '']" role="tabpanel">
								<h4>Максимально подробно опишите ситуацию</h4>
								<form @submit.prevent="onSubmitRequest" class="contact-form" method="post" accept-charset="utf-8">
									<div class="form-group">
										<label for="requestTextFree" class="control-label">Описание услуги</label>
										<textarea
											id="requestTextFree"
											v-model="requestTextFree"
											rows="10"
											class="form-control"
											@blur="$v.$touch()"
										></textarea>
										<small
											class="text-danger"
											v-if="$v.requestTextFree.$dirty && !$v.requestTextFree.maxLength"
										>Поле должно быть не более {{$v.requestTextFree.$params.maxLength.max}} символов</small>
									</div>
									<input type="submit" class="btn btn-primary" name="submit" value="Отправить">
								</form>
							</div>
						</div>
					</v-modal>

					<v-modal
						v-if="modal == 'selectServiceModal'"
						@modalclose="modalClose"
					>
						<h2>Выбор услуги</h2>
						<label for="service" class="control-label">Список категорий</label>
						<select id="service" v-model="serviceGroupId" class="form-control">
							<option value="0">-- Не выбрано --</option>
							<option :value="group.id"
							:key="index"
							v-for="(group, index) in SERVICES_GROUPS"
							>{{ group.title }}</option>
						</select>
						<template v-if="serviceGroupId">
							<label class="control-label mt-4">Список услуг</label>
							<table class="table">
								<thead>
									<tr>
										<th>Услуга</th>
										<th>Цена</th>
										<th>Ед. измерения</th>
									</tr>
								</thead>
								<tbody>
									<tr :key="index" v-for="(service, index) in servicesOfGroup">
										<td><a @click="setServiceId(service.id)" href="javascript:;">{{ service.title }}</a></td>
										<td>{{ service.price }} руб.</td>
										<td>{{ service.unit }}</td>
									</tr>
								</tbody>
							</table>
						</template>
						<a href="javascript:;" class="btn btn-secondary mt-5" @click="modal='serviceModal'">Отмена</a>
					</v-modal>

					<v-modal
						v-if="modal == 'showServiceRequestModal'"
						@modalclose="modalClose"
						classmodal="modal--calendar"
					>
						<h2>Просмотр заявки</h2>
						<table class="table">
							<thead>
								<tr>
									<th></th>
									<th>Услуга</th>
									<th>Цена, руб.</th>
									<th>Ед.измерения</th>
									<th>Кол-во</th>
									<th>Итого, руб.</th>
									<th>Дата и время исполнения</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>Плановые данные</th>
									<td>{{ showenServiceRequest.attributes.planning.title }}</td>
									<td>{{ showenServiceRequest.attributes.planning.price }}</td>
									<td>{{ showenServiceRequest.attributes.planning.unit }}</td>
									<td>{{ showenServiceRequest.attributes.planning.quantity }}</td>
									<td>{{ showenServiceRequest.attributes.planning.price*showenServiceRequest.attributes.planning.quantity }}</td>
									<td>{{ showenServiceRequest.attributes.planning.execution_date | moment('dddd, Do MMMM') }} -
										{{ showenServiceRequest.attributes.planning.execution_hours }}
										{{ declOfNum(showenServiceRequest.attributes.planning.execution_hours, ['час', 'часа', 'часов']) }}</td>
								</tr>
								<tr>
									<th>Фактические данные</th>
									<td>{{ showenServiceRequest.attributes.title }}</td>
									<td>{{ showenServiceRequest.attributes.price }}</td>
									<td>{{ showenServiceRequest.attributes.unit }}</td>
									<td>{{ showenServiceRequest.attributes.quantity }}</td>
									<td>{{ showenServiceRequest.attributes.price*showenServiceRequest.attributes.quantity }}</td>
									<td>{{ showenServiceRequest.attributes.execution_date | moment('dddd, Do MMMM') }} -
										{{ showenServiceRequest.attributes.execution_hours }}
										{{ declOfNum(showenServiceRequest.attributes.planning.execution_hours, ['час', 'часа', 'часов']) }}</td>
								</tr>
							</tbody>
						</table>
					</v-modal>

					<v-modal
						v-if="modal == 'selectExecutionDateModal'"
						@modalclose="modalClose"
						classmodal="modal--calendar"
					>
						<FullCalendar
							:options="calendarOptions"
							:events="EVENTS"
						/>
						<div class="form-calendar" v-if="state.formCalendarShow">
							<div class="form-group">
								<p class="form-control-static">{{ serviceTitle }}</p>
							</div>
							<div class="form-group mb-4">
								<strong>Дата:</strong> {{ tempData.executionDate | moment('dddd, Do MMMM') }} <br><br>
								<strong>Время:</strong><br>
								от <vue-timepicker
										v-model="tempData.executionTime.from"
										hide-disabled-items
										close-on-complete
										auto-scroll
										input-width="100px"
										:hour-range="hourRange"
										hour-label="часы"
										minute-label="мин"
										:minute-range="[0]"
									>
									<template v-slot:icon>
											<img src="https://i.postimg.cc/CLkZcW46/custom-clock.png">
										</template>
									</vue-timepicker>
								до <vue-timepicker
										v-model="tempData.executionTime.to"
										hide-disabled-items
										close-on-complete
										auto-scroll
										input-width="100px"
										:hour-range="hourRange"
										hour-label="часы"
										minute-label="мин"
										:disabled="!tempData.executionTime.from || tempData.executionTime.from >= tempData.executionTime.to"
										:minute-range="[0]"
									>
										<template v-slot:icon>
											<img src="https://i.postimg.cc/CLkZcW46/custom-clock.png">
										</template>
									</vue-timepicker>
							</div>
							<div class="form-group">
								<a href="javascript:;" class="btn" @click="clickedData=null;state.formCalendarShow=false">Отмена</a>
								<button
									:disabled="!tempData.executionTime.from || tempData.executionTime.from >= tempData.executionTime.to"
									class="btn btn-primary"
									@click="addExecutionDate"
								>Добавить</button>
							</div>
						</div>
						<div class="form-group mt-5">
							<a href="javascript:;" class="btn" @click="cancelExecutionDate">Отмена</a>
							<button :disabled="!clickedData || isEditServiceRequest"
									class="btn btn-primary"
									@click="saveExecutionDate"
							>Сохранить</button>
						</div>
					</v-modal>

					<table class="table table-striped">
						<thead>
							<tr>
								<th class="hidden-mobi">Номер заявки</th>
								<th class="hidden-mobi">Дата подачи</th>
								<th class="requests_descrip">Описание</th>
								<th>Статус</th>
								<th>Действие</th>
							</tr>
						</thead>
						<tbody>
							<tr :key="index" v-for="(serviceRequest, index) in SERVICE_REQUESTS">
								<td class="hidden-mobi">{{ serviceRequest.id }}</td>
								<td class="hidden-mobi">{{ serviceRequest.attributes.created_at | moment('DD.MM.YYYY') }}</td>
								<td class="requests_descrip">{{ serviceRequest.attributes.title }}</td>
								<td :title="serviceRequest.attributes.status_title"
									v-html="serviceRequest.attributes.status"
								></td>
								<td>
									<template v-if="serviceRequest.attributes.status_id === 0">
										<a href="javascript:;"
										   @click.prevent="editShowModal(index)"
										   class="btn btn-primary"
										>Изменить</a>
										<a href="javascript:;"
										   @click.prevent="disabledServiceRequest(index)"
										   class="btn btn-danger"
										>Отменить</a>
									</template>
									<template v-else-if="serviceRequest.attributes.status_id === 4"></template>
									<template v-else-if="serviceRequest.attributes.status_id === 3">
										<template v-if=" ! serviceRequest.attributes.rating">
											<star-rating
												v-model="rating"
												:star-size="20"
											></star-rating>
											<a href="javascript:;"
											   @click.prevent="setRating(index)"
											   class="btn btn-success btn-rating"
											   :class="{'disabled': !rating}"
											>Оценить</a>
										</template>
										<template v-else>
											<star-rating
												:rating="serviceRequest.attributes.rating"
												:star-size="20"
												:read-only="true"
											></star-rating>
										</template>
									</template>
									<template v-else>
										<a href="javascript:;"
										   @click.prevent="showServiceRequest(index)"
										   class="btn btn-success"
										>Просмотр</a>
									</template>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- <h4>Задачи</h4>
				<ul>
					<li>Заказывать услуги УК, выбирая из списка услуг или в свободной форме</li>
					<li>Видеть статус заявки</li>
					<li>Получать счета на оплату</li>
					<li>Оплачивать счета через сервис</li>
					<li>Видеть историю оказанных услуг</li>
					<li>Видеть список заявок и акты выполненных работ</li>
					<li>Видеть тарифы и прайс-лист на услуги</li>
				</ul>-->
		</div>
	</article>
</template>

<script>
	import { required, minLength, maxLength} from 'vuelidate/lib/validators'
	import stringUtils from '@app/helpers/utils/string.utils'
	import { mapActions, mapGetters } from 'vuex'
    import StarRating from 'vue-star-rating'
	import FullCalendar from '@fullcalendar/vue'
	import dayGridPlugin from '@fullcalendar/daygrid'
	import timeGridPlugin from '@fullcalendar/timegrid'
	import interactionPlugin from '@fullcalendar/interaction'
	import ruLocale from '@fullcalendar/core/locales/ru'
	import moment from 'moment'
	import VueTimepicker from 'vue2-timepicker'
	import 'vue2-timepicker/dist/VueTimepicker.css'

	export default {

		data() {
			return {
				myUniqueId: null,
				showModal: false,
				modal: null,
				serviceModalHeader: '',
				requestText: null,
				requestTextFree: null,
				quantity: 1,
				serviceGroupId: 0,
				serviceId: 0,
				servicesOfGroup: null,
				executionDate: null,
				executionTime: {
					from: null,
					to: null
				},
				executionHours: 0,
				rating: 0,
				activeTab: 0,
				state: {
					servicesLoading: true,
					serviceRequestsLoading: true,
					formCalendarShow: false,
					selectExecutionDateBtnDisabled: true,
					isEditServiceRequest: false,
					addedExecutionDate: false
				},
				eventsData: [],
				clickedData: null,
				calendar: {
					startTime: '9:00',
					endTime: '19:00'
				},
				tempData: {
					executionDate: null,
					executionTime: {
						from: null,
						to: null
					},
					executionHours: 0
				},
				showenServiceRequest: null
			}
		},

		components: {
			StarRating,
			FullCalendar,
			VueTimepicker
		},

		validations: {
			requestText: {
				maxLength: maxLength(500),
			},
			requestTextFree: {
				maxLength: maxLength(500),
			},
		},

		computed: {
			...mapGetters([
				'SERVICE_REQUESTS',
				'SERVICES',
				'SERVICES_GROUPS',
				'EVENTS'
			]),

			serviceTitle() {
				if (this.serviceId) {
					let service = this.SERVICES.filter(item => {
						return item.id === this.serviceId;
					});
					this.state.selectExecutionDateBtnDisabled = false;
					return service[0].title +', стоимость '+ service[0].price +' руб. / 1 ' + service[0].unit;
				} else {
					this.state.selectExecutionDateBtnDisabled = true;
					return '-- Выбрать услугу --';
				}
			},
			totalPrice() {
				if (this.serviceId) {
					let service = this.SERVICES.filter(item => {
						return item.id === this.serviceId;
					});
					// console.log(service)
					return service[0].price * this.quantity;
				} else {
					return 0;
				}
			},
			executionDateTitle() {
				if (this.executionDate) {
					return moment(this.executionDate).format('LLL');
				} else {
					return '-- Выбрать дату и время --';
				}
			},
			calendarOptions () {
				return {
					plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
					headerToolbar: {
						left: 'prev,next today',
						center: 'title',
						right: 'timeGridWeek,dayGridMonth'
					},
					initialView: 'timeGridWeek',
					eventClick: this.handleEventClick,
					dateClick: this.handleDayClick,
					eventDrop: this.handleEventDrop,
					eventResize: this.handleEventResize,
					locale: 'ruLocale',
					firstDay: 1,
					dayMaxEvents: true,
					businessHours: {
						// days of week. an array of zero-based day of week integers (0=Sunday)
						daysOfWeek: [ 1, 2, 3, 4, 5 ], // Monday - Friday
						startTime: this.calendar.startTime, // a start time (10am in this example)
						endTime: this.calendar.endTime, // an end time (6pm in this example)
					},
					weekends: true,
					slotMinTime: this.calendar.startTime,
					slotMaxTime: this.calendar.endTime,
					slotLabelFormat: {
						hour: "numeric",
						minute: "2-digit",
						omitZeroMinute: false
					}, //side time labels
					slotDuration: '00:60:00',
					slotLabelInterval: 60,
					contentHeight: "auto",
					buttonText: {
						today: 'Сегодня',
						month: 'Месяц',
						week: 'Неделя',
						day: 'День',
						list: 'Список'
					},
					editable: false,
					eventOverlap: false,
					events: this.filteredEventsData
				}
			},

			filteredEventsData() {
				let eventsData = this.eventsData;
				return eventsData.filter(item => {
					return item.group_id === this.serviceGroupId
				});
			},

			hourRange() {
				let startTime = this.calendar.startTime.split(':');
				let endTime = this.calendar.endTime.split(':');

				return [[startTime[0], endTime[0]]];
				//
				// Отключено
				//
				let diff = endTime[0]-startTime[0];

				let arr = new Array();
				for (let i=0; i<=diff; i++) {
					arr[i] = +startTime[0] + i;
				}
				return arr;
			}
		},

		watch: {
			serviceGroupId: function (val) {
				if (val !== 0) {
					this.servicesOfGroup = this.SERVICES.filter(item => {
						return item.group_id === val
					});
				}
			},
			clickedData: function (val) {
				this.tempData.executionDate = this.clickedData;
				this.tempData.executionTime.from = moment(this.clickedData).format('HH:mm');
				this.tempData.executionTime.to = moment(this.clickedData).add(1, 'hours').format('HH:mm');
			}
		},

		methods: {
			...mapActions([
				'saveRequestService',
				'getServiceRequests',
				'getServices',
				'getServicesGroups',
				'setRequestServiceIsNotActive',
				'setRequestServiceRating',
				'getEvents',
			]),

			async onSubmitRequest() {
				if (this.$v.$invalid) {
					this.$v.$touch();
					return;
				}

				let data = {
					title: this.serviceTitle,
					quantity: this.quantity,
					service_id: this.serviceId,
					execution_date: this.executionDate,
					description: this.requestText,
					text_free: this.requestTextFree,
					execution_hours: this.executionHours
				};

				await this.saveRequestService(data);

				// Обновляем ключ
				this.myUniqueId = this.getUniqueId();

				this.modal = '';
			},

			modalClose() {
				this.modal = null;
			},

			setShowModal() {
				this.modal = 'serviceModal';
				this.isEditServiceRequest = false;

				this.serviceModalHeader = 'Подача заявки';

				this.requestText = null;
				this.quantity = 1;
				this.serviceId = 0;
				this.executionDate = null;
			},

			editShowModal(index) {
				this.modal = 'serviceModal';
				this.isEditServiceRequest = true;
				this.state.formCalendarShow = true;

				this.serviceModalHeader = 'Редактирование заявки';

				let request = this.SERVICE_REQUESTS.filter((item, key) => {
					return key === index
				});

				let attr = request[0].attributes;

				// this.eventsData = this.eventsData.filter(item => item.id != request[0].id);

				// this.eventsData.forEach(item => {
				// 	if (item.id === request[0].id) {
				// 		item.editable = "true";
				// 	}
				// });

				let startFrom = moment(attr.execution_date).format('HH:mm');
				let endTo = moment(attr.execution_date)
					.add(attr.execution_hours, 'hours')
					.format('HH:mm');

				this.myUniqueId = request[0].id;
				this.requestText = attr.description;
				this.quantity = attr.quantity || 1;
				this.serviceId = attr.service_id;
				this.serviceGroupId = attr.group_id;
				this.executionDate = this.tempData.executionDate = attr.execution_date;
				this.executionTime.from = this.tempData.executionTime.from = startFrom;
				this.executionTime.to = this.tempData.executionTime.to = endTo;
				this.executionHours = this.tempData.executionHours = attr.execution_hours;
			},

			async disabledServiceRequest(index) {
				if (confirm('Вы подтверждаете отмену заявки? Возобновить будет невозможно!')) {
					let request = this.SERVICE_REQUESTS.filter((item, key) => {
						return key === index
					});

					let attr = request[0];

					await this.setRequestServiceIsNotActive(attr.id);
				} else {
					return false;
				}
			},

			showServiceRequest(index) {
				this.modal = 'showServiceRequestModal';

				let request = this.SERVICE_REQUESTS.filter((item, key) => {
					return key === index
				});

				this.showenServiceRequest = request[0];
			},

			async servicesRefresh() {
				await this.getServices();
				this.state.serviceRequestsLoading = true;
				await this.getServiceRequests();
				this.state.serviceRequestsLoading = false;
			},

			setRating(index) {
				let request = this.SERVICE_REQUESTS.filter((item, key) => {
					return key === index
				});

				let attr = request[0];
				let data = {
					id: attr.id,
					rating: this.rating
				};
				// console.log(data);
				this.setRequestServiceRating(data);
			},

			activateTab(index) {
				this.activeTab = index
			},

			selectService() {
				this.modal = 'selectServiceModal';
			},

			setServiceId(serviceId) {
				this.serviceId = serviceId;
				this.modal = 'serviceModal';
			},

			selectExecutionDate() {
				if ( ! this.state.selectExecutionDateBtnDisabled) {
					this.modal = 'selectExecutionDateModal';

					// if (this.isEditServiceRequest) {
					// 	this.eventsData.forEach(item => {
					// 		if (item.id === this.myUniqueId) {
					// 			item.editable = "true";
					// 		}
					// 	});
					// }
				}
			},

			handleEventClick(clickInfo) {
				if (! this.clickedData) {
					alert('Это время уже занято!');
					return false;
				}
			},

			handleDayClick(dateClicked, allDay, jsEvent, view) {
				if ( ! this.state.addedExecutionDate) {
					const date = moment(dateClicked.dateStr);
					const today = moment();

					if (date.diff(today) <= 0) {
						alert('Нельзя выбрать это время!');
						return false;
					}

					// let isWeekend = false;
					//
					// if (date.isoWeekday() == 6 || date.isoWeekday() == 7) {
					// 	isWeekend = true;
					// }
					// date.add(1, 'day');
					//
					// if (isWeekend) {
					// 	alert('Нельзя выбрать выходной!');
					// 	return false;
					// }

					// if (! this.clickedData) {
					this.clickedData = dateClicked.dateStr;
					// console.log(dateClicked.dateStr)
					this.state.formCalendarShow = true;
					// } else {
					// 	return false;
					// }
				}
			},

			handleEventDrop(event) {
				// console.log('drop-event', event);

				let date = moment(this.tempData.executionDate).format('YYYY-MM-DD');
				let start = date +' '+ this.tempData.executionTime.from;
				let end = date +' '+ this.tempData.executionTime.to;

				let executionDate = moment(this.tempData.executionDate)
					.add(event.delta.years, 'years')
					.add(event.delta.months, 'months')
					.add(event.delta.days, 'days')
					.add(event.delta.milliseconds, 'milliseconds')
					.format('YYYY-MM-DD HH:mm');

				let startFrom = moment(start)
					.add(event.delta.milliseconds, 'milliseconds')
					.format('HH:mm');
				let endTo = moment(end)
					.add(event.delta.milliseconds, 'milliseconds')
					.format('HH:mm');

				this.tempData.executionDate = executionDate;
				this.tempData.executionTime.from = startFrom;
				this.tempData.executionTime.to = endTo;
			},

			handleEventResize(event) {
				// console.log('resize-event', event);

				let date = moment(this.tempData.executionDate).format('YYYY-MM-DD');
				let start = date +' '+ this.tempData.executionTime.from;
				let end = date +' '+ this.tempData.executionTime.to;
				let endTo = moment(end)
					.add(event.endDelta.milliseconds, 'milliseconds');

				let duration = moment.duration(moment(endTo).diff(moment(start)));
				this.tempData.executionHours = duration.asHours();

				this.tempData.executionTime.to = endTo.format('HH:mm');
			},

			async fetchEvents() {
				this.state.calendarLoading = true;
				await this.getEvents();
				this.state.calendarLoading = false;
			},

			addExecutionDate() {
				if (this.tempData.executionTime.from
					|| this.tempData.executionTime.from < this.tempData.executionTime.to) {
					this.tempData.executionDate = moment(this.clickedData).format('YYYY-MM-DD HH:mm');

					this.state.addedExecutionDate = true;
					let date = moment(this.tempData.executionDate).format('YYYY-MM-DD');
					let start = date +' '+ this.tempData.executionTime.from;
					let end = date +' '+ this.tempData.executionTime.to;

					let duration = moment.duration(moment(end).diff(moment(start)));
					this.tempData.executionHours = duration.asHours();

					this.eventsData.push({
						id: this.myUniqueId,
						title: 'Заявка: '+ this.serviceTitle,
						start: start,
						end: end,
						group_id: this.serviceGroupId,
						editable: true
					});
					this.state.formCalendarShow = false;
				}
			},

			cancelExecutionDate() {
				this.tempData.executionDate = null;
				this.tempData.executionTime.from = null;
				this.tempData.executionTime.to = null;
				this.tempData.executionHours = null;

				this.modal = 'serviceModal';
				this.state.formCalendarShow = false;
				this.state.addedExecutionDate = false;

				if (this.clickedData) {
					this.clickedData = null;
					this.eventsData.splice(-1, 1);
				}
			},

			saveExecutionDate() {
				let date = moment(this.tempData.executionDate).format('YYYY-MM-DD');
				let start = date +' '+ this.tempData.executionTime.from;
				let end = date +' '+ this.tempData.executionTime.to;

				if (this.clickedData || this.isEditServiceRequest) {
					this.eventsData.splice(-1, 1);
					this.eventsData.push({
						id: this.myUniqueId,
						title: 'Заявка: ' + this.serviceTitle,
						start: start,
						end: end,
						group_id: this.serviceGroupId,
						editable: true
					});

					this.executionDate = this.tempData.executionDate;
					this.executionTime.from = this.tempData.executionTime.from;
					this.executionTime.to = this.tempData.executionTime.to;
					this.executionHours = this.tempData.executionHours;

					this.state.formCalendarShow = false;
					this.modal = 'serviceModal';
				} else {
					alert('Выберите дату и время!')
				}
			},

			getUniqueId() {
				return '_' + Math.random().toString(36).substr(2, 9);
			},

			declOfNum(n, textForms) {
				return stringUtils.declOfNum(n, textForms)
			}
		},

		async mounted() {
			if (this.SERVICE_REQUESTS.length === 0) {
				await this.getServiceRequests();
			}
			if (this.SERVICES.length === 0) {
				await this.getServices();
			}
			if (this.SERVICES_GROUPS.length === 0) {
				await this.getServicesGroups();
			}

			this.state.servicesLoading = false;
			this.state.serviceRequestsLoading = false;

			this.$store.commit('setDocumentTitle', 'Заявки');

			await this.fetchEvents();

			this.myUniqueId = this.getUniqueId();

			this.EVENTS.servicesRequest.forEach((servicesRequest) => {
				let executionDateEnd = moment(servicesRequest.execution_date)
					.add(servicesRequest.execution_hours, 'hours')
					.format('YYYY-MM-DD HH:mm');

				this.eventsData.push({
					id: servicesRequest.id,
					title: 'Заявка: '+ servicesRequest.title,
					description: servicesRequest.description,
					start: servicesRequest.execution_date,
					end: executionDateEnd,
					group_id: servicesRequest.group_id
				});
			});
		}
	}
</script>

<style lang="sass" scoped="">
	.badge
		font-size: 100%
		font-weight: normal

	.btn-add-request
		margin-bottom: 20px

	.btn-rating
		margin-top: 10px

	.form-calendar
		position: absolute
		left: 10%
		top: 30%
		z-index: 1000
		max-width: 300px
		padding: 20px
		background-color: #fff
		background-clip: padding-box
		/*border: 1px solid rgba(0,0,0,.2)*/
		box-shadow: 0 24px 38px 3px rgba(0,0,0,0.14), 0 9px 46px 8px rgba(0,0,0,0.12), 0 11px 15px -7px rgba(0,0,0,0.2)
</style>
