# SHEREADS — Project Master Specification

## 1. Project Identity

**Project Name:** SHEREADS

**Purpose:**  
SHEREADS is an online book discovery and reading platform designed primarily for girls and women in Afghanistan.

The platform helps users discover books that match their interests through a personalized onboarding questionnaire. A visitor answers a series of questions, and the system analyzes her answers to recommend relevant books.

After discovering recommended books, the visitor can create an account and continue using SHEREADS through her personalized dashboard.

SHEREADS is not simply a book CRUD system. Its core purpose is:

**Questionnaire → Interests → Personalized Recommendations → Book Discovery → Reading**

---

# 2. Technology Stack

The application will be developed using:

### Backend
- PHP
- Laravel
- Laravel Livewire
- Laravel Blade
- Laravel Eloquent ORM
- MySQL

### Frontend
- HTML
- CSS
- JavaScript
- Blade
- Livewire
- Alpine.js where appropriate
- Tailwind CSS where appropriate

### UI/UX
The application's existing Figma design is the primary source of truth for the interface.

Do not redesign existing screens unless there is a specific requirement to do so.

---

# 3. Brand / Design System

## Primary Color

```text
#05653D
```

## Secondary Color

```text
#E7B944
```

## Font

```text
Afacad
```

The SHEREADS visual identity should consistently use these brand colors and typography while following the existing Figma design.

The Figma design takes priority over arbitrary implementation decisions.

Design implementation should maintain:

- Typography
- Colors
- Spacing
- Border radius
- Buttons
- Cards
- Forms
- Navigation
- Icons
- Responsive behavior
- Layout structure

---

# 4. Development Philosophy

Build SHEREADS as a maintainable production Laravel application.

Prioritize:

- Clean architecture
- Laravel conventions
- Reusable components
- Database integrity
- Proper relationships
- Separation of responsibilities
- Security
- Performance
- Scalability
- Maintainability

Avoid unnecessary complexity.

Do not introduce repositories, interfaces, services, packages, or architectural patterns simply for the sake of abstraction.

Use Laravel's native features when they are sufficient.

---

# 5. Claude's Role

Act as a senior Laravel + Livewire software architect and developer.

When working on SHEREADS:

- Analyze requirements before coding.
- Think about database structure before creating migrations.
- Follow Laravel conventions.
- Follow Livewire best practices.
- Follow the existing Figma design.
- Create reusable components.
- Avoid duplicated code.
- Keep business logic out of Blade templates.
- Keep complex business logic out of Livewire components when it belongs in a service/action.
- Protect all administrative functionality with proper authorization.
- Validate all user input.
- Consider performance and database indexing.
- Identify weak requirements and explain why they are problematic.
- Do not blindly implement technically poor decisions.
- Do not make major architectural decisions silently.

If a requirement is ambiguous and materially affects the architecture, explain the ambiguity and propose the best solution before implementation.

---

# 6. Existing Figma Design

The SHEREADS dashboard and application interfaces have already been designed in Figma.

Figma should be treated as the visual source of truth.

When implementing a screen:

1. Inspect the relevant Figma design.
2. Identify the layout structure.
3. Identify reusable components.
4. Identify typography and spacing.
5. Identify responsive behavior.
6. Implement the design using reusable Blade/Livewire components.
7. Avoid unnecessary visual deviations.

Do not create an entirely new design when a Figma screen already exists.

---

# 7. Main Visitor Experience

When a new visitor enters SHEREADS, she should experience the following flow:

```text
Get Started Page
        ↓
Get Started Button
        ↓
Questionnaire
        ↓
All Done Page
        ↓
Recommendation Processing
        ↓
Suggested Books
        ↓
Signup
        ↓
User Dashboard
        ↓
Personalized Recommendations
```

The questionnaire is completed before registration.

The visitor should not be required to create an account before answering the questionnaire.

---

# 8. Get Started Page

The first page should introduce SHEREADS and encourage the visitor to discover books personalized for her.

The page should contain a prominent:

**Get Started**

button.

Clicking the button begins the questionnaire.

The exact UI must follow the Figma design.

---

# 9. Questionnaire

The onboarding questionnaire is one of the most important features of SHEREADS.

The initial system should contain approximately 5–6 questions.

The exact number should be configurable.

Each question currently has approximately four answer options.

Questions and answer options should be stored in the database rather than hard-coded into Blade templates.

The questionnaire should support:

- Questions
- Answer options
- Ordering
- Active/inactive status
- Recommendation signals
- Future expansion

---

# 10. Example Questionnaire

Example:

**Question:**

> What type of books are you most interested in?

Possible answers:

- Personal Development
- Fiction
- Education
- History

Another question might ask about:

- Career interests
- Personal development
- Education
- Hobbies
- Literature
- Psychology
- Social topics
- Learning goals

The actual questions should be configurable through the admin dashboard.

---

# 11. Questionnaire Data Architecture

Do not hard-code recommendation logic directly into individual questions.

A conceptual structure should be:

```text
Question
    ↓
Answer Option
    ↓
Interest / Recommendation Signal
    ↓
Book
```

This is intentionally more flexible than:

```text
Question
    ↓
Category
    ↓
Book
```

The second approach is too rigid.

An answer should be capable of influencing multiple interests.

For example:

```text
Answer:
"I want to improve my career."

        ↓

Interests:
Career
Leadership
Personal Development
Communication
```

A book can also have multiple relevant interests.

---

# 12. Recommendation Engine

The recommendation system is the core business logic of SHEREADS.

The system should analyze questionnaire answers and generate a ranked list of books.

The recommendation engine should use a scoring mechanism.

Conceptually:

```text
User Answer
      ↓
Related Interests
      ↓
Book Interests
      ↓
Matching Score
      ↓
Rank Books
      ↓
Return Recommended Books
```

For example:

```text
User selects:
Leadership

Book A:
Leadership + Career + Management

Book B:
Fiction + Romance

Book C:
Leadership + Personal Development
```

Book A and Book C should receive stronger recommendation scores than Book B.

The exact scoring algorithm should be implemented in a dedicated service, such as:

```text
RecommendationService
```

Do not place the recommendation algorithm directly inside a Livewire component.

---

# 13. Recommendation Signals / Interests

SHEREADS should have a concept of interests, tags, or recommendation signals.

Possible examples:

```text
Personal Development
Leadership
Career
Education
Psychology
History
Literature
Fiction
Communication
Entrepreneurship
Motivation
Relationships
Science
```

These should be database-driven.

A future admin should be able to manage them without modifying application code.

Possible conceptual relationships:

```text
Answer Option
    ↕
Interests

Book
    ↕
Interests
```

This allows the recommendation system to remain flexible.

---

# 14. Temporary Questionnaire Responses

A visitor completes the questionnaire before creating an account.

Therefore, her answers must temporarily exist before registration.

Possible implementation approaches include:

- Session storage
- Temporary database records
- Questionnaire response records

Choose the approach that provides the best balance of reliability, maintainability, and simplicity.

After registration, questionnaire responses must be associated with the newly created user.

The user should not have to answer the questionnaire again.

---

# 15. All Done Page

After completing the questionnaire, the visitor should see an **All Done** page.

The page should communicate that the questionnaire has been completed and personalized recommendations are being prepared.

A loading or processing state may be displayed.

After processing, the visitor should be taken to the suggested books page.

---

# 16. Suggested Books Before Registration

A key SHEREADS feature is that visitors should see recommended books before creating an account.

Flow:

```text
Complete Questionnaire
        ↓
Calculate Recommendations
        ↓
Suggested Books
        ↓
Visitor sees recommendations
        ↓
Signup
```

This allows SHEREADS to demonstrate its value before asking the visitor to register.

The recommendation results should remain available through the onboarding flow.

---

# 17. Signup

After viewing suggested books, the visitor should be able to create an account.

Initial signup fields:

- Full Name
- Email
- Password
- Confirm Password

After successful registration:

```text
Visitor
   ↓
Account Created
   ↓
Questionnaire Responses Attached
   ↓
Personalized User Profile
   ↓
Dashboard
```

The user should not have to repeat the questionnaire.

---

# 18. Authentication

The application should provide:

- Registration
- Login
- Logout
- Password hashing
- Password confirmation
- Password reset
- Session security

Use Laravel's authentication conventions.

Do not build custom authentication unnecessarily.

---

# 19. User Roles

SHEREADS will have multiple roles.

Initial roles:

```text
Super Admin
Admin
Editor
Writer
Subscriber / Reader
```

## Super Admin

Full system access.

Can manage:

- Users
- Roles
- Permissions
- Books
- Authors
- Categories
- Questions
- Answer options
- Interests
- Recommendations
- Settings
- Platform content

## Admin

General administrative access according to assigned permissions.

## Editor

Primarily manages and reviews content.

Potential permissions:

- Manage books
- Manage authors
- Manage categories
- Edit content
- Review content
- Publish content

## Writer

Primarily creates or contributes content.

Potential permissions:

- Create content
- Edit assigned content
- Submit content for review

## Subscriber / Reader

Normal registered SHEREADS user.

Can:

- View recommendations
- Browse books
- Read books
- Save books
- Favorite books
- Create collections
- Manage profile
- Access Reading Room

Authorization must be enforced at the backend level.

Do not rely only on hiding dashboard buttons.

---

# 20. Users

The user domain should conceptually contain:

```text
User
 ├── Role
 ├── Profile
 ├── Questionnaire Responses
 ├── Favorites
 ├── Saved Books
 ├── Collections
 ├── Reading Progress
 └── Reading History
```

Do not turn the users table into a container for unrelated data.

Use appropriate related tables when information represents a separate domain concept.

---

# 21. Books

Books are one of the primary entities in SHEREADS.

A book may contain:

- Title
- Slug
- Description
- Cover image
- Author(s)
- Category/categories
- Interest/recommendation tags
- Language
- Publication information
- Reading content
- Status
- Visibility
- Created by
- Updated by

The final fields should be determined from the actual Figma dashboard and content requirements.

---

# 22. Book Categories

Books should have categories.

Examples:

```text
Fiction
Education
Personal Development
History
Literature
Science
Biography
Psychology
```

Categories should be manageable from the admin dashboard.

Possible fields:

- Name
- Slug
- Description
- Image/icon where required
- Status
- Sort/order position

Books may belong to multiple categories.

Use a many-to-many relationship where appropriate.

---

# 23. Authors

Authors should have their own database entity.

Possible fields:

- Name
- Slug
- Biography
- Profile image
- Country
- Status

A book may have one or multiple authors depending on requirements.

Do not simply store author names as plain text in the books table if author management is required.

---

# 24. Book Interests

Books should be associated with recommendation interests/signals.

Example:

```text
Book:
"Atomic Habits"

Interests:
- Personal Development
- Habits
- Productivity
- Psychology
```

This is separate from categories.

A category answers:

> "What type of book is this?"

An interest answers:

> "Why might this book be relevant to this particular user?"

This distinction is important for the recommendation engine.

---

# 25. Favorites

Users can favorite books.

Conceptually:

```text
User ↔ Favorite ↔ Book
```

Potential table:

```text
favorites
```

Fields:

```text
id
user_id
book_id
created_at
updated_at
```

A user must not be able to create duplicate favorites for the same book.

Use a database-level unique constraint on:

```text
user_id + book_id
```

---

# 26. Saved Books

Users can save books for later.

Saved books and favorites should remain separate concepts.

### Favorite

> I really like this book.

### Saved

> I want to keep this book to read later.

Potential table:

```text
saved_books
```

Use a unique constraint on:

```text
user_id + book_id
```

---

# 27. Collections

Users can create their own book collections.

Examples:

```text
Books I Want to Read
My Favorite Books
University Books
Personal Development
```

Conceptually:

```text
User
  ↓
Collections
  ↓
Collection Books
  ↓
Books
```

Potential tables:

```text
collections
collection_book
```

A collection should belong to a user.

Possible collection fields:

- Name
- Description
- User ID
- Visibility
- Created at
- Updated at

---

# 28. Reading Room

The Reading Room is where users actually read books online.

The reading experience should be comfortable and focused.

Potential features:

- Book title
- Author
- Book content
- Chapter/page navigation
- Previous/next navigation
- Reading progress
- Bookmark/current position
- Continue Reading
- Reading history

The exact reading implementation depends on the final book format.

Possible formats include:

- HTML-based book content
- PDF
- EPUB
- Another supported format

Do not finalize the reader architecture until the actual book content format is known.

---

# 29. Reading Progress

SHEREADS should remember where a user stopped reading.

Conceptually:

```text
User
+
Book
+
Current Position
+
Progress
+
Last Read At
```

Example:

```text
User has read:
Book A → Chapter 4 → Page 63
```

When she returns:

```text
Continue Reading
```

should return her to the previous position.

---

# 30. Reading History

The system should keep basic reading activity.

Possible information:

- User
- Book
- Started at
- Last opened at
- Current progress
- Completed status
- Completed at

This can later support:

- Continue Reading
- Recently Read
- Reading statistics
- Better personalization

---

# 31. User Dashboard

The dashboard should provide access to:

- Personalized recommendations
- Recommended books
- Saved books
- Favorite books
- Collections
- Reading Room
- Recently read books
- Reading progress
- Profile/settings

The exact layout should follow the Figma design.

---

# 32. Admin Dashboard

The admin dashboard should provide management functionality.

Expected areas include:

```text
Dashboard
Users
Roles / Permissions
Books
Categories
Authors
Interests
Questions
Answer Options
Recommendations
Reading Activity
Settings
```

Additional sections may be added when requirements justify them.

---

# 33. Book Management

Authorized staff should be able to:

- Create books
- Edit books
- Delete/archive books
- Upload covers
- Add authors
- Assign categories
- Assign interests
- Add descriptions
- Add reading content
- Publish/unpublish books
- Search books
- Filter books
- Sort books

All operations require proper validation and authorization.

---

# 34. Questionnaire Management

Administrators should be able to manage:

### Questions

- Create
- Edit
- Delete
- Reorder
- Activate/deactivate

### Answer Options

- Create
- Edit
- Delete
- Reorder
- Activate/deactivate

### Recommendation Signals

Answers should be associated with relevant interests/signals.

---

# 35. Recommendation Management

Recommendation logic should be database-driven as much as reasonably possible.

For example:

```text
Answer Option
      ↓
Interest(s)
      ↓
Book Interest(s)
      ↓
Recommendation Score
```

Avoid hard-coded logic such as:

```php
if ($answer === 'A') {
    // return these specific books
}
```

The system should allow future administrators to modify recommendation relationships without changing application code.

---

# 36. Suggested Initial Database Entities

The initial architecture may include:

```text
users

roles
permissions
role_user
permission_role

books
authors
categories
book_category

interests
book_interest
answer_interest

questions
question_options
questionnaire_responses
questionnaire_answers

favorites
saved_books

collections
collection_book

reading_progress
reading_history
```

This is a starting point, not an instruction to blindly create every table.

Before migrations are generated, review the complete ERD and relationship design.

---

# 37. Database Principles

Use:

- Foreign keys
- Proper indexes
- Unique constraints
- Timestamps
- Soft deletes where appropriate
- Proper cascading behavior
- Pivot tables for many-to-many relationships

Avoid:

- Comma-separated IDs
- Duplicate data
- Relationships stored in JSON when normalized relationships are appropriate
- Hard-coded IDs
- Unnecessary JSON structures
- Database fields containing multiple unrelated values

---

# 38. Laravel Project Structure

A possible structure:

```text
app/
├── Livewire/
│   ├── Frontend/
│   └── Backend/
│
├── Models/
├── Services/
├── Actions/
├── Policies/
└── Support/
```

Views:

```text
resources/views/
├── layouts/
├── components/
├── livewire/
│   ├── frontend/
│   └── backend/
└── pages/
```

The exact structure may be adapted to the existing project.

---

# 39. Livewire Rules

Use Livewire where interactivity provides value.

Good candidates:

- Questionnaire
- Search
- Filtering
- Favorites
- Saved books
- Collections
- Admin CRUD
- Modals
- Dynamic forms
- Reading progress
- Dashboard interactions

Do not use Livewire simply because it is available.

Standard Laravel routes/controllers are acceptable where Livewire adds no meaningful benefit.

---

# 40. Reusable Components

Create reusable UI components where appropriate.

Examples:

```text
Button
Input
Textarea
Select
Modal
Alert
BookCard
AuthorCard
CategoryCard
InterestBadge
Pagination
SearchInput
Dropdown
Badge
EmptyState
LoadingState
```

Avoid copying identical markup throughout the application.

---

# 41. Validation

All user-controlled data must be validated.

This includes:

- Registration
- Login
- Profile updates
- Books
- Authors
- Categories
- Questions
- Answer options
- Interests
- Collections
- Search/filter input where necessary
- File uploads

Validation should provide useful error messages.

---

# 42. Authorization

Authorization must be enforced server-side.

Do not rely solely on:

```blade
@if(...)
```

to protect functionality.

Use appropriate Laravel:

- Policies
- Gates
- Middleware
- Permissions

A user must not be able to bypass authorization by manually calling a route or Livewire action.

---

# 43. Security

Follow Laravel security best practices.

Important areas:

- Password hashing
- CSRF protection
- Authorization
- Input validation
- File upload validation
- Secure file storage
- Mass assignment protection
- Authentication security
- Rate limiting where appropriate
- Protection of private reading/user data

Never expose sensitive application information unnecessarily.

---

# 44. File Storage

Books may have:

- Cover images
- Author images
- Reading files
- Other media

Use Laravel's filesystem abstraction.

Do not store large files directly in database fields unless there is a justified reason.

The storage design should be compatible with future growth.

---

# 45. Search

Books should eventually support searching by:

- Title
- Author
- Category
- Description
- Interests

The initial implementation may use MySQL.

The architecture should not prevent moving to a dedicated search engine later.

---

# 46. Status Management

Content should use explicit statuses where appropriate.

Example:

```text
Draft
Published
Archived
```

Use archive/soft-delete behavior when appropriate instead of permanently deleting important content.

---

# 47. Slugs

Public resources should use slugs where appropriate.

Examples:

```text
/books/atomic-habits
/authors/james-clear
/categories/personal-development
```

Slugs should be unique and generated safely.

---

# 48. Performance

Pay attention to:

- N+1 queries
- Eager loading
- Pagination
- Database indexes
- Query optimization
- Caching where useful
- Large collections
- File loading

Example:

```php
Book::with(['authors', 'categories', 'interests'])->paginate();
```

should generally be preferred over querying relationships repeatedly inside loops.

---

# 49. Responsive Design

SHEREADS must work on:

- Desktop
- Laptop
- Tablet
- Mobile

Do not simply shrink desktop layouts.

Mobile layouts should remain usable and should follow the responsive behavior defined by the Figma design.

---

# 50. Accessibility

Use reasonable accessibility practices:

- Semantic HTML
- Labels for form fields
- Keyboard accessibility
- Focus states
- Sufficient contrast
- Accessible buttons
- Meaningful error messages
- Appropriate alt text
- Proper heading hierarchy

---

# 51. Error Handling

Provide clear feedback for:

- Successful actions
- Validation errors
- Failed operations
- Unauthorized actions
- Missing resources

Use Laravel and Livewire conventions for errors and flash messages.

---

# 52. Development Phases

Development should happen incrementally.

## Phase 1 — Foundation

- Laravel configuration
- Database configuration
- Authentication
- Users
- Roles/permissions
- Base layouts
- Brand styles
- Reusable UI components

## Phase 2 — Onboarding

- Get Started
- Questions
- Answer options
- Temporary questionnaire responses
- All Done
- Recommendation engine
- Suggested books

## Phase 3 — Registration

- Signup
- Login
- Logout
- Password reset
- Attach questionnaire responses to user

## Phase 4 — User Dashboard

- Dashboard
- Recommendations
- Profile
- Favorites
- Saved books
- Collections

## Phase 5 — Library

- Books
- Categories
- Authors
- Interests
- Search
- Filters

## Phase 6 — Reading Room

- Book reader
- Reading progress
- Continue reading
- Reading history

## Phase 7 — Admin Dashboard

- Users
- Roles
- Books
- Categories
- Authors
- Interests
- Questions
- Answer options
- Recommendation management
- Settings

## Phase 8 — Optimization

- Security review
- Performance optimization
- Responsive testing
- Accessibility testing
- Query optimization
- UX refinement

---

# 53. Critical Development Rule

Do NOT build the entire project in one step.

For every major feature:

1. Understand the requirement.
2. Inspect the relevant Figma design.
3. Identify database requirements.
4. Identify models and relationships.
5. Identify authorization requirements.
6. Identify Livewire components.
7. Identify reusable UI components.
8. Explain the proposed implementation.
9. Implement the feature.
10. Test the feature.
11. Review the implementation before moving to the next feature.

---

# 54. Database First for Major Features

Before creating migrations for a major feature, first explain:

- Entities
- Relationships
- Foreign keys
- Cardinality
- Unique constraints
- Indexes
- Delete behavior
- Status fields
- Potential future requirements

Then propose the ERD.

Only after the architecture is reviewed should migrations be generated.

---

# 55. Do Not Overengineer

Production quality does not mean unnecessary complexity.

Avoid creating:

- Repositories without a real need
- Interfaces without multiple implementations
- Excessive service classes
- Excessive abstractions
- Unnecessary third-party packages
- Complex patterns where Laravel already provides a clean solution

The goal is:

**Simple enough to maintain, structured enough to scale.**

---

# 56. Core SHEREADS Architecture

The most important conceptual architecture is:

```text
                    ┌───────────────┐
                    │    Visitor    │
                    └───────┬───────┘
                            │
                            ▼
                    ┌───────────────┐
                    │ Questionnaire │
                    └───────┬───────┘
                            │
                            ▼
                    ┌───────────────┐
                    │    Answers    │
                    └───────┬───────┘
                            │
                            ▼
                    ┌───────────────┐
                    │   Interests   │
                    └───────┬───────┘
                            │
                            ▼
                    ┌───────────────┐
                    │ Recommendation│
                    │    Engine     │
                    └───────┬───────┘
                            │
                            ▼
                    ┌───────────────┐
                    │ Suggested     │
                    │    Books      │
                    └───────┬───────┘
                            │
                            ▼
                    ┌───────────────┐
                    │    Signup     │
                    └───────┬───────┘
                            │
                            ▼
                    ┌───────────────┐
                    │ User Dashboard│
                    └───────┬───────┘
                            │
             ┌──────────────┼──────────────┐
             ▼              ▼              ▼
         Favorites       Saved          Collections
             │              │              │
             └──────────────┼──────────────┘
                            ▼
                    ┌───────────────┐
                    │ Reading Room  │
                    └───────┬───────┘
                            │
                            ▼
                    ┌───────────────┐
                    │Reading History│
                    │ & Progress    │
                    └───────────────┘
```

---

# 57. Central Business Principle

The central purpose of SHEREADS is not simply to store books.

The core value is personalized discovery.

The system should continuously connect:

```text
User
 ↓
Interests
 ↓
Books
 ↓
Reading Behavior
 ↓
Future Personalization
```

The initial recommendation system can be relatively simple, but its architecture should allow it to become more sophisticated later.

---

# 58. Future Features

Possible future features include:

- Advanced recommendations
- Ratings
- Reviews
- Comments
- Bookmarks
- Reading goals
- Reading statistics
- Achievements
- Notifications
- Community features
- Multiple languages
- Mobile applications
- Advanced search
- Analytics
- Content moderation

Do not implement these features unless specifically requested.

However, do not make architectural decisions that unnecessarily prevent them later.

---

# 59. Current Priority

The first implementation priority is the complete onboarding experience:

```text
Get Started
    ↓
Questionnaire
    ↓
All Done
    ↓
Suggested Books
    ↓
Signup
    ↓
Dashboard
    ↓
Recommended Books
```

This flow should work correctly before building every secondary feature.

---

# 60. Final Instruction to Claude

When developing SHEREADS, always think in terms of:

**User Experience + Business Logic + Database Integrity + Laravel Architecture + Figma Accuracy.**

Do not rush into writing code.

First understand the requirement and architecture.

When there is a weak technical decision, say so clearly and propose a better alternative.

When there are multiple valid approaches, compare them briefly and recommend one.

Do not invent major business requirements.

Do not implement future features prematurely.

Build SHEREADS incrementally, test each major feature, and keep the codebase clean throughout development.