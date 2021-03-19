<template>
	<article class="content">
		<header class="content_header">
			<h1 class="content_title">Вопрос-ответ</h1>
		</header>
		<div class="content_body">

			<a href="javascript:;" class="btn btn-success" @click="showModal=true">Задать вопрос</a>

			<v-modal
				v-if="showModal"
				@modalclose="modalClose"
			>
				<h3>Задать вопрос</h3>
				<br>
				<form @submit.prevent="onSubmitFaq" class="contact-form" method="post" accept-charset="utf-8">
					<div class="form-group">
						<label for="name">Представьтесь, пожалуйста <span class="requred" title="Обязательно для заполнения">*</span></label>
						<input type="text" v-model.trim="name" id="name" class="form-control" required="">
						<small
							class="text-danger"
							v-if="$v.name.$dirty && ! $v.name.required"
						>Поле обязательно для заполнения</small>
					</div>
					<div class="form-group">
						<label for="email">Email <span class="requred" title="Обязательно для заполнения">*</span></label>
						<input type="email" v-model.trim="email" id="email" class="form-control" required="">
						<small
							class="text-danger"
							v-if="$v.email.$dirty && ! $v.email.required"
						>Поле обязательно для заполнения</small>
					</div>
					<div class="form-group">
						<label for="question">Ваш вопрос <span class="requred" title="Обязательно для заполнения">*</span></label>
						<textarea v-model.trim="question" id="question" rows="5" class="form-control" required=""></textarea>
						<small
							class="text-danger"
							v-if="$v.question.$dirty && !$v.question.minLength"
						>Поле должно быть не менее {{$v.question.$params.minLength.min}} символов</small>
						<small
							class="text-danger"
							v-if="$v.question.$dirty && !$v.question.maxLength"
						>Поле должно быть не более {{$v.question.$params.maxLength.max}} символов</small>
						<small
							class="text-danger"
							v-if="$v.question.$dirty && ! $v.question.required"
						>Поле обязательно для заполнения</small>
					</div>
					<div class="form-group text-center">
						<input type="submit" class="btn btn-primary" name="submit" value="Отправить">
					</div>
				</form>
			</v-modal>

			<header class="content_header">
				<h2 class="content_title">{{ FAQ_CATEGORY_DATA.title }}</h2>
			</header>
			<v-spinner
				v-if="state.faqLoading"
				:size="spinner_size"
				:speed="spinner_speed"
				:message="spinner_message"
			/>
			<template v-else>
				<template v-if="FAQ_CATEGORY.length > 0">

					<ul id="faq-list" class="faq_list">

						<li class="faq_item" :key="index" v-for="(faq, index) in FAQ_CATEGORY">
							<a class="faq_item-question"
							   href="javascript:;"
							   @click="faqShow(index)"
							>
								{{ faq.attributes.question }}
							</a>
							<transition name="slide">
								<div class="faq_item-answer" v-if="state.faqIsActive == index" v-html="faq.attributes.answer"></div>
							</transition>
						</li>

					</ul>
				</template>
			</template>
		</div>
	</article>
</template>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import route from '@app/route'
	import { required, minLength, maxLength } from 'vuelidate/lib/validators'

	export default {
		data() {
			return {
				showModal: false,
				spinner_size: 24,
				spinner_speed: 0.8,
				spinner_message: 'Загрузка',
				name: null,
				email: null,
				question: null,
				state: {
					faqLoading: true,
					faqIsActive: 0,
				},
			}
		},

		validations: {
			name: {
				required
			},
			email: {
				required
			},
			question: {
				required,
				minLength: minLength(5),
				maxLength: maxLength(800),
			},
		},

		computed: {
			...mapGetters([
				'FAQ_CATEGORY_DATA',
				'FAQ_CATEGORY',
			]),
		},

		methods: {
			...mapActions([
				'getFaqCategoryData',
				'saveQuestion',
			]),

			modalClose() {
				this.showModal = false;
			},

			async fetchFaq(page_url) {
				this.state.faqLoading = true;
				await this.getFaqCategoryData(page_url);
				this.state.faqLoading = false;

				this.$store.commit('setDocumentTitle', 'Вопрос-ответ категории - ' + this.FAQ_CATEGORY_DATA.title);
			},

			faqShow(index) {
				this.state.faqIsActive = this.state.faqIsActive === index ? null : index;
			},

			async onSubmitFaq() {
				if (this.$v.$invalid) {
					this.$v.$touch();
					return;
				}

				this.question = await this.saveQuestion({
					name: this.name,
					email: this.email,
					question: this.question,
				});
				this.showModal = false;
			}
		},

		watch: {
			$route(to, from) {
				this.state.faqIsActive = 0;
				this.fetchFaq(route('api.faq.category.show', this.$route.params.category_id));
			}
		},

		async mounted() {
			if (this.$route.params.category_id) {
				await this.fetchFaq(route('api.faq.category.show', this.$route.params.category_id));
			} else {
				await this.fetchFaq(route('api.faq'));
			}

		}
	}
</script>

<style lang="sass" scoped="">
	.faq_item
		margin-top: 20px

	.faq_item-question
		display: block
		margin-bottom: 10px
		font-size: 18px
		font-family: RobotoBold, sans-serif, arial, verdana, Lucida Sans

	.faq_item-answer
		padding-left: 20px
		border-left: 3px solid #eee
</style>
