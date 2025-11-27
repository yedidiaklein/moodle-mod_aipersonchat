<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Hebrew language strings for AI Person Chat module.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['ai_context_prompt'] = 'בהתבסס על המידע מ: {$a->url}

אישיות: {$a->personname}
תקופה: {$a->era}

שיחה קודמת:
{$a->history}

אתה {$a->personname}, דמות היסטורית מ{$a->era}. השב להודעה הבאה כאילו אתה האדם הזה, תוך שימוש בידע שלך על חייו, אישיותו וההקשר ההיסטורי. שמור על תגובות מעניינות אך מדויקות היסטורית. אם הנושא לא קשור לתקופתך או למומחיותך ואם restricttopic מופעל, הפנה בנימוס את השיחה חזרה לנושאים שהיית מכיר.

הודעת המשתמש: {$a->message}

אנא השב בתור {$a->personname}:';

$string['ai_system_prompt'] = 'אתה {$a->personname}, דמות היסטורית מ{$a->era}. עליך להשיב לשאלות כאילו אתה האדם הזה, תוך שימוש במידע שסופק על חייך ותקופתך. הישאר בדמות ודון רק בנושאים הקשורים לתקופה ההיסטורית שלך, חוויות החיים שלך ותחומי המומחיות שלך. אם נשאלת על נושאים מודרניים או דברים שמחוץ להקשר ההיסטורי שלך, הפנה בנימוס את השיחה חזרה לתקופתך ולחוויותיך. שמור על תגובות שיחתיות וחינוכיות, כיוון שאתה מדבר עם תלמידים שרוצים ללמוד על ההיסטוריה דרכך.';
$string['ai_topic_restriction'] = 'אני יכול לדון רק בנושאים הקשורים לחיי ולתקופה ההיסטורית שלי. אנא שאל אותי משהו על {$a->era} או על החוויות שלי באותה תקופה.';
$string['aipersonchat:addinstance'] = 'הוספת פעילות צ\'אט AI עם אישיות חדשה';
$string['aipersonchat:chat'] = 'שליחת הודעות בצ\'אט AI עם אישיות';
$string['aipersonchat:view'] = 'צפייה בפעילות צ\'אט AI עם אישיות';
$string['aipersonchatname'] = 'שם הפעילות';
$string['aipersonchatname_help'] = 'הזן שם לפעילות צ\'אט AI עם אישיות זו.';
$string['chat'] = 'צ\'אט';
$string['chatbehavior'] = 'הגדרות התנהגות צ\'אט';
$string['chatting_with'] = 'בצ\'אט עם {$a}';
$string['era'] = 'תקופה';
$string['imageurl'] = 'קישור לתמונת האישיות';
$string['imageurl_help'] = 'הזן קישור לתמונה של האישיות ההיסטורית (דיוקן, פסל וכו\').';
$string['invalidimageurl'] = 'הקישור שסופק לא נראה כקישור תקין לתמונה.';
$string['learnmore'] = 'למד עוד';
$string['max_messages_reached'] = 'הגעת למספר המקסימלי של הודעות לפעילות זו.';
$string['maxmessages'] = 'מספר הודעות מקסימלי לתלמיד';
$string['maxmessages_help'] = 'המספר המקסימלי של הודעות שכל תלמיד יכול לשלוח בפעילות זו.';
$string['message_too_long'] = 'ההודעה ארוכה מדי. אורך מקסימלי מותר הוא {$a} תווים.';
$string['modulename'] = 'צ\'אט AI עם אישיות';
$string['modulenameplural'] = 'צ\'אטים AI עם אישיות';
$string['personconfig'] = 'הגדרת אישיות היסטורית';
$string['personera'] = 'תקופה/עידן';
$string['personera_help'] = 'התקופה ההיסטורית או העידן שבו חי האדם הזה (למשל, "רומא העתיקה", "הרנסנס", "המאה ה-19").';
$string['personname'] = 'שם האישיות ההיסטורית';
$string['personname_help'] = 'הזן את שמו של האדם ההיסטורי שהתלמידים יתכתבו איתו.';
$string['personurl'] = 'קישור למידע';
$string['personurl_help'] = 'קישור למידע ביוגרפי על האדם הזה (למשל, עמוד ויקיפדיה).';
$string['pluginname'] = 'צ\'אט AI עם אישיות';
$string['privacy:metadata:aipersonchat_messages'] = 'מידע על הודעות שנשלחו על ידי משתמשים בפעילויות צ\'אט AI עם אישיות.';
$string['privacy:metadata:aipersonchat_messages:message'] = 'תוכן ההודעה שנשלחה על ידי המשתמש.';
$string['privacy:metadata:aipersonchat_messages:response'] = 'תגובת ה-AI להודעת המשתמש.';
$string['privacy:metadata:aipersonchat_messages:timecreated'] = 'הזמן שבו נוצרה ההודעה.';
$string['privacy:metadata:aipersonchat_messages:timeresponse'] = 'הזמן שבו נוצרה תגובת ה-AI.';
$string['privacy:metadata:aipersonchat_messages:userid'] = 'מזהה המשתמש ששלח את ההודעה.';
$string['privacy:metadata:core_ai'] = 'פעילות צ\'אט AI עם אישיות מתקשרת עם מערכת ה-AI כדי ליצור תגובות להודעות משתמשים.';
$string['privacy:metadata:core_ai:component'] = 'הרכיב המבקש יצירת AI.';
$string['privacy:metadata:core_ai:contextid'] = 'מזהה ההקשר שבו מתרחשת יצירת ה-AI.';
$string['privacy:metadata:core_ai:prompttext'] = 'הפרומפט שנשלח למערכת ה-AI, כולל הודעת המשתמש וההקשר.';
$string['privacy:metadata:core_ai:userid'] = 'מזהה המשתמש המבקש יצירת AI.';
$string['restricttopic'] = 'הגבל למומחיות האישיות';
$string['restricttopic_help'] = 'כאשר מופעל, ה-AI ינסה לשמור על שיחות ממוקדות בנושאים שהאישיות ההיסטורית הייתה יודעת עליהם.';
$string['send'] = 'שלח';
$string['typemessage'] = 'הקלד את ההודעה שלך כאן...';
