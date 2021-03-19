import axios from 'axios'
import route from '@app/route'
import Vue from 'vue'

const state = {
	services: [],
	servicesGroups: [],
	serviceRequests: [],
	countServicesRequest: 0,
	countServicesRequestsRequest: 0,
};

const getters = {
	SERVICES(state) {
		return state.services
	},
	SERVICES_GROUPS(state) {
		return state.servicesGroups
	},
	SERVICE_REQUESTS(state) {
		return state.serviceRequests
	},
};

const mutations = {
	SET_SERVICES: (state, services) => {
		state.services = services.data.data;
	},
	SET_SERVICES_GROUPS: (state, group) => {
		state.servicesGroups = group.data.data;
	},
	SET_SERVICE_REQUESTS(state, serviceRequests) {
		state.serviceRequests = serviceRequests.data.data;
	},
	SAVE_SERVICE_REQUESTS: (state, serviceRequest) => {
		// state.serviceRequests.push(serviceRequest.data.data);
		state.serviceRequests.unshift(serviceRequest.data.data);
	},
	UPDATE_SERVICE_REQUESTS: (state, serviceRequest) => {
		return state.serviceRequests.map((item) => {
			// console.log(item, serviceRequest);
			if (item.id == serviceRequest.data.data.id) {
				item.attributes.status = serviceRequest.data.data.attributes.status;
				item.attributes.status_id = serviceRequest.data.data.attributes.status_id;
				item.attributes.rating = serviceRequest.data.data.attributes.rating;
				return item;
			}
		});
	},
	INCREMENT_SERVICES_COUNT_REQUEST: (state) => {
		state.countServicesRequest++;
	},
	INCREMENT_SERVICES_COUNT_REQUESTS_REQUEST: (state) => {
		state.countServicesRequestsRequest++;
	},
};

const actions = {
	async getServices({dispatch, commit, state, rootState}) {
		return await axios.get(route('api.services'))
			.then((response) => {
				commit('SET_SERVICES', response);
				return response;
			})
			.catch((e) => {
				if (state.countServicesRequest < rootState.maxRequests) {
					setTimeout(function() {
						dispatch('getServices')
					}, rootState.maxRequestTiming);
					commit('INCREMENT_SERVICES_COUNT_REQUEST');
				} else {
					commit('setError', e)
					throw e
				}
			})
	},
	async getServicesGroups({dispatch, commit, state, rootState}) {
		return await axios.get(route('api.services.groups'))
			.then((response) => {
				commit('SET_SERVICES_GROUPS', response);
				return response;
			})
			.catch((e) => {
				if (state.countServicesRequest < rootState.maxRequests) {
					setTimeout(function() {
						dispatch('getServicesGroups')
					}, rootState.maxRequestTiming);
					commit('INCREMENT_SERVICES_COUNT_REQUEST');
				} else {
					commit('setError', e)
					throw e
				}
			})
	},
	async getServiceRequests({dispatch, commit, state, rootState}) {
		return await axios.get(route('api.services.requests'))
			.then((response) => {
				commit('SET_SERVICE_REQUESTS', response);
				return response;
			})
			.catch((e) => {
				if (state.countServicesRequestsRequest < rootState.maxRequests) {
					setTimeout(function() {
						dispatch('getServiceRequests')
					}, rootState.maxRequestTiming);
					commit('INCREMENT_SERVICES_COUNT_REQUESTS_REQUEST');
				} else {
					commit('setError', e)
					throw e
				}
			})
	},
	async saveRequestService({commit}, requestData) {
		return await axios.post(route('api.services.requests.store'), requestData)
			.then(response => {
				if (response.data.status == 'error') {
					Vue.notify({
						group: 'app',
						type: 'error',
						text: response.data.message
					});
					return requestData;
				} else {
					Vue.notify({
						group: 'app',
						type: 'success',
						text: 'Успешно сохранено!'
					});
					commit('SAVE_SERVICE_REQUESTS', response);
					return null;
				}
			})
			.catch((error) => {
				if (error && error.response.status === 422) {
					let errors = error.response.data.errors || {};
					commit('setErrors', errors)
				}
				// return requestData;
			});
	},
	async setRequestServiceIsNotActive({commit}, id) {
		return await axios.post(route('api.services.requests.not_active'), {
				id: id
			})
			.then(response => {
				if (response.data.status == 'error') {
					Vue.notify({
						group: 'app',
						type: 'error',
						text: response.data.message
					});
					return id;
				} else {
					Vue.notify({
						group: 'app',
						type: 'success',
						text: 'Успешно изменено!'
					});
					commit('UPDATE_SERVICE_REQUESTS', response);
					return null;
				}
			})
			.catch((error) => {
				console.log(error);return;
				if (error && error.response.status === 422) {
					let errors = error.response.data.errors || {};
					commit('setErrors', errors)
				}
				// return requestData;
			});
	},
	async setRequestServiceRating({commit}, data) {
		return await axios.post(route('api.services.requests.set_rating'), data)
			.then(response => {
				if (response.data.status == 'error') {
					Vue.notify({
						group: 'app',
						type: 'error',
						text: response.data.message
					});
					return;
				} else {
					Vue.notify({
						group: 'app',
						type: 'success',
						text: 'Успешно оценено!'
					});
					commit('UPDATE_SERVICE_REQUESTS', response);
					return null;
				}
			})
			.catch((error) => {
				console.log(error);return;
				if (error && error.response.status === 422) {
					let errors = error.response.data.errors || {};
					commit('setErrors', errors)
				}
				// return requestData;
			});
	},
};

export default {
	state,
	getters,
	mutations,
	actions
}
