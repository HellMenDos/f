<template>
	<aside class="l-sidebar" role="complementary">
		<div class="sidebar">
			<section class="widget">
				<header class="widget_header">
					<h4>Категории</h4>
				</header>
				<div class="widget_body">
					<div class="list-group">
						<router-link
							tag="a"
							class="list-group-item d-flex justify-content-between align-items-center"
							:key="index"
							active-class="active"
							v-for="(category, index) in FAQ_CATEGORIES"
							:to="{name: 'faq_category_show', params: {category_id: category.id}}"
						>{{ category.title }}</router-link>
					</div>
				</div>
			</section>
		</div>
	</aside>
</template>

<script>
	import { mapActions, mapGetters } from 'vuex'

	export default {
		data() {
			return {

			}
		},

		computed: {
			...mapGetters([
				'FAQ_CATEGORIES',
			]),
		},

		methods: {

			...mapActions([
				'getFaqCategories',
			]),

		},

		async mounted() {
			await this.getFaqCategories();
		}
	}
</script>

<style lang="sass" scoped="">

</style>
