import axios from 'axios'
import route from '@app/route'
import Vue from "vue";

const state = {
	faq: [],
	countFaqRequest: 0,
	faqCategories: [],
	faqCategory: [],
	faqCategoryData: {},
	countFaqCategoriesRequest: 0,
};

const getters = {
	FAQ(state) {
		return state.faq
	},
	FAQ_CATEGORIES(state) {
		return state.faqCategories
	},
	FAQ_CATEGORY_DATA(state) {
		return state.faqCategoryData
	},
	FAQ_CATEGORY(state) {
		return state.faqCategory
	},
};

const mutations = {
	SET_FAQ: (state, faq) => {
		state.faq = faq.data.data;
	},
	SET_FAQ_CATEGORIES: (state, categories) => {
		state.faqCategories = categories.data;
	},
	SET_FAQ_CATEGORY: (state, faq) => {
		state.faqCategory = faq.data.data;
	},
	SET_FAQ_CATEGORY_DATA: (state, faq) => {
		state.faqCategoryData = faq.data.relationships.category;
	},
	INCREMENT_FAQ_COUNT_REQUEST: (state) => {
		state.countFaqRequest++;
	},
	INCREMENT_FAQ_CATEGORIES_COUNT_REQUEST: (state) => {
		state.countFaqCategoriesRequest++;
	},
};

const actions = {
	async getFaq({dispatch, commit, state, rootState}, page_url) {
		return await axios.get(page_url)
			.then((response) => {
				commit('SET_FAQ', response);
				return response;
			})
			.catch((e) => {
				if (state.countFaqRequest < rootState.maxRequests) {
					setTimeout(function() {
						dispatch('getFaq')
					}, rootState.maxRequestTiming);
					commit('INCREMENT_FAQ_COUNT_REQUEST');
				} else {
					commit('setError', e)
					throw e
				}
			})
	},
	async getFaqCategoryData({dispatch, commit, state, rootState}, page_url) {
		return await axios.get(page_url)
			.then((response) => {
				commit('SET_FAQ_CATEGORY', response);
				commit('SET_FAQ_CATEGORY_DATA', response);
				return response;
			})
			.catch((e) => {
				if (state.countFaqRequest < rootState.maxRequests) {
					setTimeout(function() {
						dispatch('getFaqCategoryData')
					}, rootState.maxRequestTiming);
					commit('INCREMENT_FAQ_COUNT_REQUEST');
				} else {
					commit('setError', e)
					throw e
				}
			})
	},
	async getFaqCategories({dispatch, commit, state, rootState}) {
		return await axios.get(route('api.faq.categories'))
			.then((response) => {
				commit('SET_FAQ_CATEGORIES', response);
				return response;
			})
			.catch((e) => {
				if (state.countFaqCategoriesRequest < rootState.maxRequests) {
					setTimeout(function() {
						dispatch('getFaqCategories')
					}, rootState.maxRequestTiming);
					commit('INCREMENT_FAQ_CATEGORIES_COUNT_REQUEST');
				} else {
					commit('setError', e);
					throw e
				}
			})
	},

	async saveQuestion({commit}, questionData) {
		return await axios.post(route('api.faq.store'), questionData)
			.then((response) => {
				if (response.status === 201) {
					Vue.notify({
						group: 'app',
						type: 'success',
						text: 'Ваш вопрос успешно отправлен!'
					});
					return null;
				}
			})
			.catch((e) => {
				if (e && e.response.status === 422) {
					let errors = e.response.data.errors || {};
					commit('setErrors', errors)
				}
				// return abuseData;
			});
	},

};

export default {
	state,
	getters,
	mutations,
	actions
}
