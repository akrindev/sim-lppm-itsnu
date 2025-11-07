# SIM-LPPM ITSNU Documentation

Welcome to the documentation directory for SIM-LPPM ITSNU (Research and Community Service Management System).

## Available Documentation

### Testing Documentation

#### 📋 [Playwright Testing Guide](./PLAYWRIGHT_TESTING_GUIDE.md)
Comprehensive end-to-end testing documentation using Playwright MCP (Multi-Context Playwright) for automated testing.

**Contents:**
- **Overview**: System architecture, technology stack, and workflows
- **User Roles**: Detailed coverage of all 9 user roles (Dosen, Dekan, Admin LPPM, Kepala LPPM, Reviewer, Rektor, etc.)
- **50+ Test Scenarios**: Including positive, negative, and edge cases
- **Playwright Code Examples**: Ready-to-use automation scripts
- **Specialized Testing**:
  - Security testing (Authorization, SQL Injection, CSRF)
  - Accessibility testing (Keyboard navigation, Screen readers)
  - Performance testing (Search, Bulk operations)
  - Mobile responsiveness
  - Email notifications
  - Data integrity
- **Best Practices**: Implementation guidelines and maintenance notes

**Quick Start:**
```bash
# Setup test environment
composer install
bun install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Test users are seeded automatically
# See PLAYWRIGHT_TESTING_GUIDE.md for credentials
```

## Other Documentation

### Database Documentation
Located in `/database/` directory:
- `erd-documentation.md` - Entity Relationship Diagram documentation
- `erd-mermaid.md` - ERD in Mermaid format
- `sequence-diagrams.md` - Workflow sequence diagrams

### Project Information
- `README.md` (root) - Project overview and setup instructions
- `.github/copilot-instructions.md` - Development guidelines

## Contributing to Documentation

When adding new documentation:

1. **Place files in appropriate directory**:
   - Testing docs → `/docs/`
   - Database docs → `/database/`
   - API docs → `/docs/api/` (if created)

2. **Follow naming conventions**:
   - Use `SCREAMING_SNAKE_CASE.md` for major guides
   - Use `kebab-case.md` for supporting docs
   - Always include `.md` extension

3. **Update this README**:
   - Add entry with brief description
   - Include link to the new document
   - Specify the audience and use case

4. **Document structure**:
   - Start with clear title and overview
   - Use proper heading hierarchy (H1 → H2 → H3)
   - Include table of contents for long docs
   - Add code examples where applicable
   - End with references or additional resources

## Documentation Standards

- **Language**: English for technical documentation, Indonesian for user-facing content
- **Code Blocks**: Always specify language for syntax highlighting
- **Examples**: Provide working, copy-paste ready examples
- **Updates**: Keep documentation in sync with code changes
- **Version**: Include version/date information for major guides

## Need Help?

- Check existing documentation first
- Review code comments in the codebase
- Contact the QA team for testing questions
- Reach out to development team for technical clarifications

---

**Last Updated**: 2024-11-02  
**Maintained By**: Development & QA Team, SIM-LPPM ITSNU
