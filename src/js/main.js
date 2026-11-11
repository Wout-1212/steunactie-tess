// Entry point for all JavaScript on the site. esbuild bundles this file and its imports into
// assets/js/site.js (npm run build, or npm run dev while working), which every page loads with defer.
//
// Every feature is progressive enhancement: it looks for its own HTML and does nothing on pages
// without it, and the site keeps working without JavaScript.
import { initLanguagePicker } from "./language-picker.js";
import { initSiteHeader } from "./site-header.js";
import { initMobileMenu } from "./mobile-menu.js";
import { initDonationAmount } from "./donation-amount.js";
import { initDonationForm } from "./donation-form.js";
import { initDonationWall } from "./donation-wall.js";
import { initShare } from "./share.js";
import { initLightbox } from "./lightbox.js";
import { initContactForm } from "./contact-form.js";
import { initCopyIban } from "./copy-iban.js";
import { initStepTracks } from "./step-track.js";
import { initReveal } from "./reveal.js";
import { initCountUp } from "./count-up.js";
import { initMedicalScale } from "./medical-scale.js";
import { initDonationStars } from "./donation-star.js";
import { initConfetti } from "./confetti.js";

// Header and navigation.
initLanguagePicker();
initSiteHeader();
initMobileMenu();

// Donating, sharing and contact.
initDonationAmount();
initDonationForm();
initDonationWall();
initShare();
initLightbox();
initContactForm();
initCopyIban();
initStepTracks();

// Motion, only for visitors who haven't asked for reduced motion (see motion.js).
initReveal();
initCountUp();
initMedicalScale();
initDonationStars();

initConfetti();
