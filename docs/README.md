# 📚 SafeTrek API Documentation

This directory contains technical documentation for the SafeTrek Backend API.

## 📄 Documents

### 1. **[validation_rules.md](validation_rules.md)** 🔐
Complete validation rules for all API endpoints. Essential for mobile team to implement client-side validation.

**Contents:**
- Authentication endpoints validation
- Trip management validation
- Guardian management validation  
- Dart/Flutter code examples
- Error handling patterns

**Use Case:** Mobile developers implementing form validation.

---

### 2. **[database_schema.md](database_schema.md)** 🗄️
Comprehensive database schema documentation with ERD diagrams.

**Contents:**
- Entity Relationship Diagram (Mermaid)
- Detailed table structures (users, guardians, trips, location_history)
- Relationships and foreign keys
- Indexes and constraints
- Sample queries

**Use Case:** Understanding data model, writing queries, database maintenance.

---

### 3. **[session_expiration_fix.md](session_expiration_fix.md)** 🔧
Debug guide for fixing "session expired" errors when ending trips.

**Contents:**
- Root cause analysis
- Mobile app checklist
- Debug logging examples
- Token lifecycle explanation
- Testing steps

**Use Case:** Mobile team debugging authentication issues.

---

## 🎯 Quick Links

**For Mobile Developers:**
1. Start with [validation_rules.md](validation_rules.md) to implement forms
2. Refer to [database_schema.md](database_schema.md) for understanding data structure
3. Use [session_expiration_fix.md](session_expiration_fix.md) if auth issues occur

**For Backend Developers:**
1. Review [database_schema.md](database_schema.md) before schema changes
2. Update [validation_rules.md](validation_rules.md) when adding new endpoints
3. Keep docs in sync with code

---

## 📝 Maintenance

**Update Frequency:**
- Update after adding new endpoints
- Update after schema migrations
- Update after changing validation rules

**Last Updated:** 2026-01-05

---

## 🔗 Related Resources

- **API Testing:** Use Postman collection (if available)
- **Code:** `/app/Http/Controllers/Api/`
- **Routes:** `/routes/api.php`
- **Migrations:** `/database/migrations/`
