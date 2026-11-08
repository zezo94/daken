
@extends('layouts.app')

@section('title', __('contact.title') . ' - DAKEN')

@section('content')

    <main class="contact-page">



        {{-- Contact Section --}}
        <section class="container contact-section">

            <div class="contact-layout">

                {{-- Introduction --}}
                <aside class="contact-intro">

                <span class="contact-intro__eyebrow">
                    DAKEN CHOCOLATE
                </span>

                    <h2>
                        {{ app()->getLocale() === 'ar'
                            ? 'يسعدنا تواصلك معنا'
                            : 'We Would Love to Hear From You' }}
                    </h2>

                    <p>
                        {{ app()->getLocale() === 'ar'
                            ? 'هل لديك استفسار عن منتجاتنا أو ترغب في مشاركة اقتراح؟ أرسل رسالتك عبر النموذج وسنكون سعداء بالاطلاع عليها.'
                            : 'Have a question about our products or a suggestion to share? Send us a message using the form.' }}
                    </p>

                    <div class="contact-intro__brand">
                        <strong>DAKEN</strong>
                        <span>CHOCOLATE</span>
                    </div>

                </aside>

                {{-- Contact Form --}}
                <div class="contact-form-card">

                    <h2>
                        {{ app()->getLocale() === 'ar'
                            ? 'أرسل رسالة'
                            : 'Send a Message' }}
                    </h2>

                    @if(session('success'))
                        <div class="contact-success" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="contact-errors" role="alert">
                            <strong>
                                {{ app()->getLocale() === 'ar'
                                    ? 'يرجى تصحيح الأخطاء التالية:'
                                    : 'Please correct the following errors:' }}
                            </strong>

                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('contact.store') }}"
                        class="contact-form"
                    >
                        @csrf

                        <div class="contact-form__row">

                            <div class="form-group">
                                <label for="contact-name">
                                    {{ __('contact.name') }}
                                </label>

                                <input
                                    id="contact-name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    maxlength="150"
                                    autocomplete="name"
                                    required
                                    @error('name') aria-invalid="true" @enderror
                                >
                            </div>

                            <div class="form-group">
                                <label for="contact-email">
                                    {{ __('contact.email') }}
                                </label>

                                <input
                                    id="contact-email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    maxlength="255"
                                    autocomplete="email"
                                    dir="ltr"
                                    required
                                    @error('email') aria-invalid="true" @enderror
                                >
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="contact-phone">
                                {{ __('contact.phone') }}
                            </label>

                            <input
                                id="contact-phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone') }}"
                                maxlength="30"
                                autocomplete="tel"
                                dir="ltr"
                                @error('phone') aria-invalid="true" @enderror
                            >
                        </div>

                        <div class="form-group">
                            <label for="contact-subject">
                                {{ __('contact.subject') }}
                            </label>

                            <input
                                id="contact-subject"
                                type="text"
                                name="subject"
                                value="{{ old('subject') }}"
                                maxlength="255"
                                required
                                @error('subject') aria-invalid="true" @enderror
                            >
                        </div>

                        <div class="form-group">
                            <label for="contact-message">
                                {{ __('contact.message') }}
                            </label>

                            <textarea
                                id="contact-message"
                                name="message"
                                minlength="10"
                                maxlength="5000"
                                rows="7"
                                required
                                @error('message') aria-invalid="true" @enderror
                        >{{ old('message') }}</textarea>
                        </div>

                        <div class="contact-form__footer">

                            <x-buttons.primary type="submit">
                                {{ __('contact.send') }}
                            </x-buttons.primary>

                            <p>
                                {{ app()->getLocale() === 'ar'
                                    ? 'سيتم إرسال رسالتك إلى فريق DAKEN.'
                                    : 'Your message will be sent to the DAKEN team.' }}
                            </p>

                        </div>

                    </form>

                </div>

            </div>

        </section>

    </main>

@endsection
