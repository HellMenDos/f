import axios from 'axios'
import route from '@app/route'

const state = {
    events: [],
    countEventsRequest: 0,
};

const getters = {
    EVENTS(state) {
        return state.events
    },
};

const mutations = {
    SET_EVENTS: (state, events) => {
        state.events = events.data.data;
    },
    INCREMENT_EVENTS_COUNT_REQUEST: (state) => {
        state.countEventsRequest++;
    },
};

const actions = {
    async getEvents({dispatch, commit, state, rootState}) {
        return await axios.get(route('api.events.show'))
            .then((response) => {
                commit('SET_EVENTS', response);
                return response;
            })
            .catch((e) => {
                if (state.countEventsRequest < rootState.maxRequests) {
                    setTimeout(function() {
                        dispatch('getEvents')
                    }, rootState.maxRequestTiming);
                    commit('INCREMENT_EVENTS_COUNT_REQUEST');
                } else {
                    commit('setError', e)
                    throw e
                }
            })
    }
};

export default {
    state,
    getters,
    mutations,
    actions
}
