<script setup>
import { Form, Head } from "@inertiajs/vue3";
import EnclosureController from "@/actions/App/Http/Controllers/EnclosureController";
import MessageController from "@/actions/App/Http/Controllers/MessageController";
import EnclosureCreatureTile from "@/components/EnclosureCreatureTile.vue";
import FitTitle from "@/components/FitTitle.vue";
import InputError from "@/components/InputError.vue";
import LcdScreen from "@/components/LcdScreen.vue";
import PageMasthead from "@/components/PageMasthead.vue";
import PixelIcon from "@/components/PixelIcon.vue";
import SectionHeading from "@/components/SectionHeading.vue";
import TextLink from "@/components/TextLink.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { frameNumber } from "@/lib/format";
import { create, edit } from "@/routes/creatures";

defineOptions({
    layout: {
        breadcrumbs: [{ title: "Enclos", href: "#" }],
        bleed: true,
    },
});

defineProps({
    owner: Object,
    isOwner: Boolean,
    creatures: Array,
    messages: Array,
});

const count = (n, one, many) => `${n} ${n > 1 ? many : one}`;
</script>

<template>
    <Head :title="`Enclos de ${owner.name}`" />

    <!-- Left page: the owner, as a magazine cover. -->
    <PageMasthead
        aria-labelledby="owner-title"
        :kicker="`Enclos n° ${frameNumber(owner.id)}`"
        :aside="`${count(creatures.length, 'créature', 'créatures')} · ${count(messages.length, 'message', 'messages')}`"
    >
        <div
            class="flex flex-col gap-6 lg:grid lg:grid-cols-12 lg:items-end lg:gap-x-6"
        >
            <div class="flex min-w-0 flex-col gap-5 lg:col-span-8">
                <FitTitle id="owner-title" :text="owner.name" />

                <figure
                    v-if="owner.motto"
                    class="relative max-w-[640px] border-l-8 border-ink pl-5"
                >
                    <blockquote
                        class="text-[clamp(1.5rem,3.4vw,2.25rem)] leading-tight font-extrabold text-balance [overflow-wrap:anywhere]"
                    >
                        «&nbsp;{{ owner.motto }}&nbsp;»
                    </blockquote>
                    <figcaption class="mt-2 kicker">
                        La devise de {{ owner.name }}
                    </figcaption>
                </figure>
                <p v-else class="kicker">
                    {{
                        isOwner
                            ? "Pas encore de devise. Choisissez-en une."
                            : "Pas de devise affichée."
                    }}
                </p>
            </div>

            <Form
                v-if="isOwner"
                v-bind="EnclosureController.update.form()"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-3 border-4 border-ink bg-paper p-4 shadow-hard-lg [--input:var(--color-ink)] lg:col-span-4"
            >
                <Label for="motto" class="kicker">Ma devise</Label>
                <Input
                    id="motto"
                    name="motto"
                    :default-value="owner.motto ?? ''"
                    class="border-ink bg-paper text-ink"
                />
                <InputError :message="errors.motto" />
                <Button type="submit" :disabled="processing" class="self-start">
                    Enregistrer
                </Button>
            </Form>
        </div>
    </PageMasthead>

    <!-- Right page: the residents, then the guestbook. -->
    <div
        class="mx-auto grid w-full max-w-[1280px] gap-10 px-4 pt-8 pb-14 sm:px-8 lg:grid-cols-12 lg:gap-8"
    >
        <section
            aria-labelledby="residents-title"
            class="flex min-w-0 flex-col gap-4 lg:col-span-7"
        >
            <SectionHeading
                id="residents-title"
                kicker="01 · Pensionnaires"
                :title="isOwner ? 'Mes créatures' : 'Ses créatures'"
            />

            <ul
                v-if="creatures.length"
                class="grid grid-cols-[repeat(auto-fill,minmax(190px,1fr))] gap-5"
            >
                <li
                    v-for="creature in creatures"
                    :key="creature.id"
                    class="flex"
                >
                    <EnclosureCreatureTile
                        :creature="creature"
                        class="w-full"
                    />
                </li>
            </ul>

            <div
                v-else
                class="flex flex-col items-start gap-4 border-4 border-dashed border-border p-5 sm:flex-row sm:items-center"
            >
                <LcdScreen
                    :grain="5"
                    class="grid size-24 shrink-0 place-items-center border-4 border-ink"
                >
                    <PixelIcon name="egg" class="lcd-blink size-14" />
                </LcdScreen>
                <div class="grid gap-2">
                    <p class="pixel-title text-[28px]">Cet enclos est vide.</p>
                    <p>Aucune créature n'habite ici pour l'instant.</p>
                    <TextLink
                        v-if="isOwner"
                        :href="create()"
                        class="self-start"
                    >
                        Adopter une créature
                    </TextLink>
                </div>
            </div>
        </section>

        <section
            aria-labelledby="guestbook-title"
            class="flex min-w-0 flex-col gap-4 lg:col-span-5"
        >
            <SectionHeading
                id="guestbook-title"
                kicker="02 · Courrier"
                title="Livre d'or"
            >
                <PixelIcon name="note" class="mb-1 size-8 shrink-0" />
            </SectionHeading>

            <ol v-if="messages.length" class="flex flex-col">
                <li
                    v-for="message in messages"
                    :key="message.id"
                    class="border-b-2 border-border py-4 first:pt-1"
                >
                    <article class="grid gap-2">
                        <header
                            class="flex flex-wrap items-baseline justify-between gap-x-3"
                        >
                            <p class="font-extrabold [overflow-wrap:anywhere]">
                                {{ message.author }}
                            </p>
                            <p
                                class="font-mono text-[13px] font-bold text-muted-foreground"
                            >
                                {{ message.date }}
                            </p>
                        </header>
                        <p class="message-body whitespace-pre-line">
                            {{ message.body }}
                        </p>
                    </article>
                </li>
            </ol>

            <p
                v-else
                class="border-b-2 border-border pb-4 text-muted-foreground"
            >
                Le livre d'or attend sa première signature.
            </p>

            <Form
                v-bind="MessageController.store.form(owner.id)"
                v-slot="{ errors, processing }"
                class="mt-2 flex flex-col gap-3 border-4 border-border bg-card p-4 shadow-hard-lg"
                reset-on-success
            >
                <Label for="body" class="kicker">Laisser un message</Label>
                <textarea
                    id="body"
                    name="body"
                    rows="4"
                    class="w-full resize-y border-2 border-input bg-background px-3 py-2 text-base font-bold shadow-[inset_3px_3px_0_0_rgb(20_16_31/0.12)] placeholder:text-muted-foreground focus-visible:shadow-hard-sm"
                />
                <p class="text-sm text-muted-foreground">
                    Le gras est permis : &lt;b&gt;comme ceci&lt;/b&gt;.
                </p>
                <InputError :message="errors.body" />
                <Button type="submit" :disabled="processing" class="self-start">
                    Envoyer
                </Button>
            </Form>
        </section>
    </div>
</template>

<style scoped>
/* Visitors write free HTML here: keep it inside the column. */
.message-body {
    min-width: 0;
    overflow-x: auto;
    overflow-wrap: anywhere;
    line-height: 1.5;
}

.message-body :deep(img),
.message-body :deep(video),
.message-body :deep(iframe) {
    max-width: 100%;
    height: auto;
}

.message-body :deep(b),
.message-body :deep(strong) {
    font-weight: 900;
}
</style>
