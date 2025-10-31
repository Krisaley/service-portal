# **Laravel Field Service Management System - Design Brief**

## **Project Overview**
A **modern, professional, and easy-to-use** multi-tenant field service management platform combining CRM, project management, compliance tracking, e-commerce, and customer portal functionality. The system serves both internal staff and external customers with role-based access levels tied to subscription tiers.

### **Key Design Principles**
- **Modern Professional SaaS Aesthetic** - Clean, polished interface with focus on usability
- **Fully White-Label** - Teams customize branding, colors, and logo
- **Highly Customizable** - Drag-drop dashboard widgets, flexible data views, saved preferences
- **Desktop-First with PWA Mobile** - Rich desktop experience with native-like mobile app
- **Easy to Use** - Intuitive navigation, instant feedback, helpful empty states
- **Fast & Responsive** - Sub-2-second page loads, optimistic UI updates, smooth animations

## **Tech Stack**

### **Core Framework**
- **Laravel** (latest stable version)
- **PHP** 8.2+
- **MySQL** database
- **Redis** (caching + queues)

### **Frontend**
- **Livewire Volt** (single-file component syntax)
- **Tailwind CSS** (utility-first styling)
- **Alpine.js** (included with Livewire)
- **WireUI** (Livewire component library)

### **Authentication & Authorization**
- **Laravel Jetstream** with Teams feature
- Role-based access control for staff and customers
- Multi-tenant architecture with team-based isolation

### **Real-time & Background Processing**
- **Laravel Echo + Pusher** (WebSocket connections)
- **Redis + Laravel Horizon** (queue management and monitoring)

---

## **Core Features & Modules**

### **1. Multi-Tenancy**
**Recommendation needed:** Choose between:
- **Option A:** Tenancy for Laravel (separate databases per tenant - most secure, best isolation)
- **Option B:** Spatie Multi-tenancy (flexible, can do shared or separate DBs)
- **Option C:** Single database with team_id filtering (simpler, Jetstream Teams built-in)

**Recommendation:** Given you're using Jetstream Teams and have staff + customer access, **Option C (Jetstream Teams)** or **Spatie Multi-tenancy with single database** would work well. This allows:
- Staff members belong to company team with admin/manager/staff roles
- Customers have their own teams with limited permissions based on subscription
- Shared resources (products, blog) across all tenants

### **2. User Management & Roles**
- **Staff roles:** Admin, Manager, Engineer, Sales, Support
- **Customer roles:** Account Owner, User (limited access)
- **Permissions:** Based on Jetstream Teams + custom extensions
- **Two-way API** access with Laravel Sanctum authentication

### **3. E-commerce & Product Catalog**
- Product/service catalog management
- Pricing tiers and variations
- **Inventory & Parts Management:**
  - Stock level tracking (SKU, quantity, location)
  - Low stock alerts and reorder points
  - Parts used tracking per job/service
  - Supplier management
  - Parts cost tracking (purchase price vs billing price)
  - Multi-location inventory (van stock, warehouse, etc.)
- Product categories and tags
- Integration with quotation system
- **Parts Usage Analytics:**
  - Most frequently used parts
  - Parts cost per asset/customer/engineer
  - Failure rate analysis (which parts fail most)
  - Predictive maintenance trends
  - ROI on preventive vs reactive maintenance

### **4. Subscription Management**
- **Note:** Payment processing external to system
- Generate PDF invoices using **Spatie LaravelPDF**
- Generate proforma invoices
- Track subscription tiers and access levels
- Subscription expiry notifications
- Customer access gating based on active subscription

### **5. Quotation & Proposal System**
**Workflow:**
```
Create Quote (one-off or catalog-based)
  → Line items with pricing
  → Apply discount (triggers approval if threshold exceeded)
  → Internal approval workflow
  → Send to customer
  → Customer e-signature/authorization
  → Convert to Job/Project
```

**Features:**
- Template-based quotes
- Product/service catalog selection
- Dynamic pricing and discounts
- Approval thresholds (by value/discount percentage)
- Version control (track quote revisions)
- E-signature integration
- PDF generation
- Email delivery

### **6. Asset Management**
Track multiple asset types:
- **Physical equipment** (company-owned tools, machinery)
- **Client assets** (items being serviced/managed)
- **Sub-contractor licenses** with expiry date tracking
- **RAMS** (Risk Assessment Method Statements)
- **Liability insurance** with expiry alerts

**Features:**
- Asset registry with custom fields per type
- **Maintenance schedules and history:**
  - Recurring service schedules (weekly, monthly, quarterly, annually, custom)
  - One-time maintenance tasks
  - Service due date tracking
  - Automated email notifications (configurable days in advance)
  - Auto-create job/ticket when service is due
  - Maintenance history log (who, when, what was done)
- **Expiry date tracking and notifications:**
  - License expiry alerts (30/14/7 days before)
  - Insurance expiry alerts
  - RAMS document expiry
  - Certification expiry
  - Configurable notification thresholds per asset type
- Document attachment (via Spatie Media Library)
- Asset assignment to jobs/engineers
- Compliance reporting (overdue services, expired items)

### **7. Ticketing System**
**Custom-built with Livewire Volt**

**Features:**
- Customer-submitted tickets
- Staff ticket creation
- Priority levels (Low, Normal, High, Urgent)
- Status tracking (New, Open, Pending, Resolved, Closed)
- Assignment to engineers/teams
- Internal notes vs customer-visible comments
- SLA tracking
- Email notifications
- File attachments

### **8. Job & Project Management**

**Kanban boards:**
- Drag-drop task management
- Custom columns/stages per workflow
- Task assignment and progress tracking
- Resource allocation (which engineers on which jobs)

**Gantt charts:**
- Project timelines
- Task dependencies
- Milestone tracking
- Critical path visualization

**Engineer management:**
- Timesheet tracking (clock in/out per job)
- PDA/mobile job access (responsive design)
- Job notes and updates
- Photo uploads from field
- Location tracking (optional, Google Maps integration)

**Resource management:**
- Engineer availability and scheduling
- Equipment allocation
- Conflict detection

### **9. Blog & Content Management**
- Post creation with rich text editor
- Categories and tags
- Featured images (Spatie Media Library)
- SEO metadata
- Publishing workflow (draft → review → published)
- Comments (optional, moderated)

### **10. Customer Dashboard**
**Limited access based on subscription tier:**
- View assigned assets
- Submit and track tickets
- View quotes and invoices
- Access job/project status
- View assigned contacts
- Download documents
- Manage team users

### **11. RESTful API**
**Two-way API with Sanctum authentication:**
- CRUD operations for all major entities
- Webhook support for external systems
- Rate limiting
- API documentation (Scribe or similar)
- Integration endpoints for:
  - Accounting software (QuickBooks, Xero, Sage)
  - CRM systems (Salesforce, HubSpot)
  - Google Services (Calendar, Drive, Maps)

### **12. Activity Logging**
**Spatie Activity Log package:**
- Track all user actions
- Audit trail for compliance
- "Who changed what and when"
- Filterable activity feeds

### **13. Email Management & Automation**
**Automated email-to-ticket/job workflow:**

**Incoming Email Monitoring:**
- IMAP/POP3 email inbox monitoring (dedicated support email)
- Email parsing and intelligent extraction:
  - Machine model/asset identification
  - Error codes detection
  - Location extraction
  - Contact name extraction
  - Problem description parsing
- Auto-create ticket or job from email
- Email threading (all replies tracked in one ticket)
- Attachment extraction and storage
- Spam/auto-reply filtering

**Email-to-Ticket Conversion:**
- Create ticket from incoming email
- Match sender to existing customer (or create new contact)
- Extract and link asset if mentioned (e.g., "LGMG AR14J")
- Parse priority from keywords (urgent, emergency, etc.)
- Auto-assign based on rules (asset type, location, keywords)
- Preserve full email thread in ticket

**Outbound Email Management:**
- Reply to customer from within ticket (tracked)
- Email templates for common responses
- Auto-notification on status changes
- CC/BCC support for internal team
- Email signature per user/team
- Track email open/read status (optional)

**Communication History:**
- Complete email thread visible in ticket/job
- Track all inbound and outbound emails
- Search across all communications
- Export email history per customer/job

**Smart Categorization:**
- Auto-categorize by keywords (breakdown, service, quote request, etc.)
- Machine learning suggestions (optional future enhancement)
- Manual override and re-categorization

**Email Workflow Rules:**
- Route emails based on sender, subject, keywords
- Auto-assign to specific engineer or team
- Escalation rules for unread emails
- SLA tracking from email receipt time

### **14. Warranty Management**
**Track warranty status and manufacturer claims:**

**Warranty Registration:**
- Link warranty to asset
- Warranty start/end dates
- Warranty terms and conditions (document upload)
- Manufacturer warranty contact info
- Coverage details (parts, labor, both)
- Exclusions and limitations

**Warranty vs Chargeable Determination:**
- Automatic warranty check when job created
- Visual indicator (badge) if asset under warranty
- Warranty eligibility rules (age, usage, type of failure)
- Override for disputed cases
- Track reason if chargeable despite warranty

**Manufacturer Claim Tracking:**
- Create warranty claim record
- Claim reference number
- Claim status workflow:
  - Submitted → Under Review → Approved/Rejected → Paid
- Track claim submission date
- Expected resolution date
- Claim documentation (photos, reports, failed parts)
- Claim amount and approval amount
- Rejection reasons and appeals

**Claim Documentation:**
- Required documents checklist per manufacturer
- Photo evidence of failed parts
- Error code logs
- Service report upload
- Customer authorization
- Proof of purchase/warranty certificate

**Manufacturer Integration:**
- API integration with manufacturer portals (optional)
- Email-based claim submission workflow
- Track claim correspondence
- Automatic status updates when possible

**Warranty Analytics:**
- Track claim success rates per manufacturer
- Average claim processing time
- Most common warranty failures
- Warranty cost recovery metrics
- Assets with frequent warranty claims

**Customer Transparency:**
- Show warranty status in customer portal
- Claim status updates
- Expected timeline for claim resolution
- Documentation of warranty coverage

### **15. Enhanced Parts Management & Purchase Orders**
**Complete inventory and procurement workflow:**

**Purchase Order System:**
- Create PO for parts from suppliers
- PO number generation (auto-increment or custom format)
- Line items with quantities and pricing
- PO status workflow:
  - Draft → Submitted → Acknowledged → In Transit → Received → Complete
- Expected delivery date tracking
- Actual delivery date recording
- Partial delivery support
- PO approval workflow (based on value threshold)

**Parts "On Order" Status:**
- Track parts on order with PO reference
- Expected arrival date
- Notify when part arrives
- Update job status when parts available
- Show "waiting for parts" status on jobs
- Alert engineer when parts ready for collection

**Supplier Management:**
- Supplier contact details
- Lead time per supplier per part
- Supplier performance tracking:
  - On-time delivery rate
  - Quality metrics
  - Price comparison
- Preferred supplier per part
- Alternative suppliers list
- Supplier payment terms

**Stock Reservation:**
- Reserve parts for specific jobs
- Prevent double-allocation
- Release reserved stock if job cancelled
- Van stock vs warehouse stock tracking
- Transfer stock between locations

**Reorder Automation:**
- Automatic PO generation when stock hits reorder point
- Configurable reorder quantity
- Seasonal adjustment suggestions
- Bulk ordering discounts tracking

**Parts Availability Checks:**
- Check stock before job assignment
- Show parts availability on job creation
- Suggest alternative parts if out of stock
- ETA for parts on order

**Delivery Tracking:**
- Track shipment status (integration with courier APIs optional)
- Delivery notification to warehouse/engineer
- Goods received note (GRN) generation
- Quality check on receipt
- Return/reject faulty deliveries

### **16. External Engineer & Sub-contractor Management**
**Manage third-party engineers and documentation:**

**Sub-contractor Database:**
- Sub-contractor company details
- Contact information
- Specializations (asset types, regions)
- Certifications and licenses (with expiry tracking)
- Insurance details (with expiry alerts)
- Payment terms and rates
- Performance ratings

**Job Assignment to External Engineers:**
- Assign jobs to sub-contractors
- Rate negotiation per job
- Purchase order for sub-contractor services
- Acceptance/rejection by sub-contractor
- Track sub-contractor availability

**Paperwork & Documentation:**
- Upload completed job sheets from external engineers
- Service reports from sub-contractors
- Photos and evidence from site
- Customer signatures (digital or scanned)
- Compliance certificates (electrical, gas, etc.)
- Risk assessments from external engineers

**Document Requirements:**
- Checklist of required documents per job type
- Auto-remind external engineer of missing docs
- Approval workflow for submitted paperwork
- Reject and request re-submission
- Archive completed documentation

**Sub-contractor Portal:**
- Limited portal access for external engineers
- View assigned jobs
- Update job status
- Upload paperwork and photos
- Submit invoices
- View payment status

**Invoice & Payment Tracking:**
- Sub-contractor invoice receipt
- Match invoice to job and PO
- Approval workflow
- Track payment status (pending, paid, overdue)
- Payment run scheduling
- Track outstanding amounts per sub-contractor

**Performance Management:**
- Rate sub-contractor per job (quality, timeliness, communication)
- Track completion rates
- Track paperwork compliance
- Customer feedback on sub-contractor work
- Performance reports for preferred supplier selection

**Compliance Tracking:**
- Insurance expiry alerts
- License/certification renewals
- Safety record tracking
- Training requirements
- Right to work documentation (if applicable)

### **17. Custom Workflow Builder**
**Create and manage custom business process workflows**

---

#### **Phase 1: Basic Configurable Workflows (Launch)**

**Purpose:** Allow teams to customize status workflows without code changes

##### **Workflow Templates**

**Pre-built Workflow Templates:**
- Warranty Claim Process (default, as documented)
- Chargeable Repair Process
- Preventive Maintenance Service
- Quote to Job Conversion
- Customer Complaint Resolution
- Equipment Installation
- Annual Service Contract Renewal

**Template Management:**
- Clone existing template to customize
- Create blank workflow from scratch
- Import/export workflows (JSON format)
- Share workflows between teams (if multi-tenant)

##### **Status Configuration**

**Custom Status Creation:**
- Define parent statuses per workflow type
- Create unlimited sub-statuses per parent status
- Set status properties:
  - Display name (customer-facing)
  - Internal name (staff view)
  - Color coding (for visual boards)
  - Icon selection
  - Customer visibility (show/hide from portal)
  - Order/sequence

**Status Transition Rules:**
- Define allowed transitions (which status can move to which)
- Prevent invalid transitions (e.g., can't go from "Closed" to "New")
- Set transition permissions by role
- Require transition notes/reasons
- Auto-log status changes in audit trail

**Example Configuration:**
```
Workflow: "Warranty Claim"

Parent Statuses:
1. New (visible to customer) → Can transition to: Job Created, Closed
2. Job Created (visible) → Can transition to: Pending Auth, Closed
3. Pending Manufacturer Auth (visible) → Can transition to: Pending Schedule, Rejected, Closed
4. Pending Schedule (visible) → Can transition to: Scheduled, Pending Parts
5. Scheduled (visible) → Can transition to: In Progress, Rescheduled
6. Repair Complete (visible) → Can transition to: Closed
7. Closed (visible) → Can transition to: Reopened

Sub-Statuses (Pending Manufacturer Auth):
- Sent to manufacturer (staff only)
- Reply received (staff only)
- Additional info required (visible)
- Photos requested (visible)
- Awaiting approval (visible)
```

##### **Auto-Assignment Rules**

**Configure Auto-Assignment Based On:**
- Status change (when status = "Pending QC Review" → assign to QC Team)
- Asset type (all "Forklift" jobs → assign to John)
- Location (all "London" jobs → assign to London team)
- Priority level (all "Urgent" → assign to Manager)
- Customer (VIP customers → assign to senior engineer)
- Time of day (out-of-hours → assign to on-call team)
- Workload balancing (round-robin assignment)

**Assignment Actions:**
- Assign to specific user
- Assign to team/queue
- Assign to user with specific skill/certification
- Assign to least busy engineer
- Escalate to manager if unassigned for X hours

##### **SLA Timer Configuration**

**Set SLAs Per Status:**
- Expected time in each status
- Warning threshold (75% of SLA)
- Breach threshold (100% of SLA)
- Escalation actions on breach

**Example SLA Configuration:**
```
Status: "Pending Manufacturer Auth"
- SLA: 5 business days
- Warning at: 4 days (send reminder to manufacturer)
- Breach at: 5 days (escalate to manager)
- Auto-escalate priority: Yes
```

**SLA Dashboard:**
- Show tickets approaching SLA breach
- Color-coded warnings (green/yellow/red)
- Manager override to extend SLA
- Pause SLA when waiting for customer

##### **Notification Configuration**

**Trigger Notifications On:**
- Status change
- Priority change
- Assignment change
- SLA warnings
- Customer reply received
- Parts arrived
- Document uploaded

**Notification Channels:**
- Email (with customizable templates)
- SMS (optional)
- Push notification (PWA)
- In-app notification
- Desktop notification (browser)
- Slack/Teams webhook (optional)

**Recipient Rules:**
- Assigned user
- Customer
- Manager
- Team members
- Custom email addresses

##### **Required Fields Per Status**

**Configure Required Information:**
- Certain fields must be filled before status transition
- Upload required documents per status
- Capture specific data points

**Example:**
```
Status: "Pending Schedule" → Cannot transition to "Scheduled" unless:
- Engineer assigned ✓
- Schedule date set ✓
- Parts allocated ✓
- Customer notified ✓
```

##### **Approval Workflows**

**Configure Approvals Based On:**
- Value thresholds (< £500 auto, > £500 approval)
- Document type (warranty claims need manager approval)
- Customer type (new customers need credit check)
- Risk level (high-risk jobs need safety approval)

**Approval Levels:**
- Single approver (line manager)
- Multi-level approval (manager → senior manager → director)
- Parallel approval (both finance AND operations)
- Majority vote (2 out of 3 approvers)

**Auto-Create Internal Tickets:**
- Generate approval request tickets
- Assign to appropriate approver
- Block parent ticket until approved
- Track approval history

---

#### **Phase 2: Advanced Visual Workflow Builder (Future Enhancement)**

**Purpose:** Drag-drop workflow designer with conditional logic and automation

##### **Visual Workflow Designer**

**Drag-Drop Canvas Interface:**
- Node-based workflow editor (similar to Zapier, n8n, Node-RED)
- Visual connections between status nodes
- Zoom/pan canvas for complex workflows
- Grid snapping for alignment
- Auto-layout option
- Export as image/PDF for documentation

**Node Types:**
1. **Status Node** - Represents a workflow status
2. **Decision Node** - Conditional branching (if/else)
3. **Action Node** - Automated actions
4. **Delay Node** - Wait for time period
5. **Approval Node** - Require approval to proceed
6. **Notification Node** - Send notifications
7. **Integration Node** - Call external API
8. **Script Node** - Custom logic (JavaScript/PHP)

**Visual Example:**
```
┌─────────────┐
│   New       │
│   Ticket    │
└──────┬──────┘
       │
       ▼
   ┌───────────────┐
   │   Decision:   │
   │ Has Warranty? │
   └───┬───────┬───┘
       │       │
    Yes│       │No
       │       │
       ▼       ▼
┌──────────┐ ┌──────────┐
│ Warranty │ │Chargeable│
│ Process  │ │ Process  │
└──────────┘ └──────────┘
```

##### **Conditional Logic Engine**

**If-Then-Else Rules:**
- Build complex conditions without coding
- Multiple condition types:
  - Field comparisons (warranty_value > 1000)
  - Date comparisons (asset_age > 2 years)
  - Text matching (error_code contains "12")
  - List membership (customer_id in VIP_list)
  - Custom calculations

**Logic Operators:**
- AND / OR / NOT
- Nested conditions
- Regular expressions
- Formula builder

**Example Conditions:**
```
IF warranty_claim_value > £1000 AND customer_type = "Standard"
THEN require_manager_approval = true
ELSE auto_approve = true

IF parts_in_stock = false AND urgency = "High"
THEN notify_purchasing_manager
AND create_express_order = true
ELSE normal_purchase_order

IF error_code IN ["12", "13", "14"] AND asset_type = "LGMG"
THEN auto_assign_to = "Hydraulics Specialist"
AND priority = "High"
```

##### **Automated Actions**

**Action Library:**
1. **Create Internal Ticket**
   - Type: Purchase requisition, approval, document request
   - Auto-assign based on rules
   - Set priority and deadline

2. **Send Email/SMS**
   - Use template or custom message
   - Variable substitution ({{customer_name}}, {{ticket_id}})
   - Attach documents
   - Schedule send time

3. **Update Field Values**
   - Set priority automatically
   - Calculate values (total_cost = parts + labour)
   - Copy data between fields
   - Format data (uppercase, date formatting)

4. **Create Related Records**
   - Auto-create job from ticket
   - Generate quote automatically
   - Create invoice on completion
   - Log activity

5. **Call Webhook/API**
   - Notify external systems
   - Sync with accounting software
   - Update CRM records
   - Post to Slack/Teams

6. **Run Custom Script**
   - Execute JavaScript/PHP code
   - Access database
   - Complex calculations
   - Data transformations

7. **Generate Documents**
   - PDF reports
   - Email summaries
   - Export data
   - Create certificates

##### **Workflow Variables**

**System Variables:**
- {{ticket.id}}
- {{ticket.customer.name}}
- {{ticket.asset.model}}
- {{ticket.created_at}}
- {{user.assigned.name}}
- {{current_status}}
- {{warranty.expiry_date}}

**Custom Variables:**
- Define per-workflow variables
- Calculate during workflow
- Pass between workflow steps
- Store for later use

**Example:**
```
Variable: total_claim_amount
Calculation: parts_cost + (labour_hours * labour_rate)
Used in condition: IF total_claim_amount > 1000 THEN...
```

##### **Multi-Path Workflows**

**Parallel Branches:**
- Split workflow into parallel paths
- Wait for all branches to complete
- Or proceed when any branch completes
- Merge branches back together

**Example:**
```
Job Completed
     │
     ├──── Branch 1: QC Review ────┐
     │                             │
     ├──── Branch 2: Invoice Sub ──┤
     │                             │
     └──── Branch 3: Update CRM ───┤
                                   │
                            ┌──────▼──────┐
                            │ All Complete│
                            │ Close Ticket│
                            └─────────────┘
```

##### **Time-Based Workflows**

**Scheduled Actions:**
- Delay for X hours/days before next step
- Wait until specific date/time
- Business hours only (skip weekends)
- Repeat actions (daily reminder until response)

**Time-Based Triggers:**
- SLA countdown timers
- Reminder escalations
- Auto-close after X days inactive
- Follow-up actions

**Example:**
```
Status: "Awaiting Customer Response"
↓
Wait 48 hours
↓
IF no response THEN send reminder email
↓
Wait 48 hours
↓
IF still no response THEN escalate to manager
```

##### **Workflow Testing & Simulation**

**Test Mode:**
- Run workflow in simulation mode
- Use test data without affecting live tickets
- Step through each node to verify logic
- See what actions would be triggered
- Validate conditions

**Workflow Analytics:**
- Average time per status
- Bottleneck detection (where tickets get stuck)
- Approval success rates
- SLA compliance rates
- Path analysis (most common route through workflow)

##### **Workflow Version Control**

**Versioning:**
- Save workflow versions
- Revert to previous version
- Compare versions (diff view)
- Clone and modify
- Publish/unpublish workflows

**Change Management:**
- Draft mode for testing
- Approval before publishing
- Gradual rollout (apply to 10% of tickets first)
- Rollback if issues detected

##### **Integration Builder**

**Connect to External Systems:**
- REST API calls (GET, POST, PUT, DELETE)
- SOAP web services
- Database queries (read-only for safety)
- FTP/SFTP file transfers
- Email parsing
- Webhook receivers

**Pre-built Integrations:**
- QuickBooks / Xero / Sage (accounting)
- HubSpot / Salesforce (CRM)
- Slack / Microsoft Teams (notifications)
- Twilio (SMS)
- SendGrid / Mailgun (email)
- Manufacturer warranty portals (custom per manufacturer)

**OAuth Authentication:**
- Secure credential storage
- Token refresh handling
- Per-team credentials (white-label)

##### **Advanced Features**

**Machine Learning Suggestions:**
- Analyze historical tickets
- Suggest next best action
- Predict completion time
- Recommend engineer assignment
- Detect anomalies

**A/B Testing:**
- Test two workflow versions
- Split traffic 50/50
- Measure performance metrics
- Auto-select winner

**Workflow Templates Marketplace:**
- Share workflows with community
- Import pre-built industry workflows
- Rate and review templates
- Industry-specific packs (HVAC, Electrical, Plumbing)

---

#### **Workflow Builder UI Components**

##### **Basic Configuration UI (Phase 1)**

**Status Manager Screen:**
```
┌────────────────────────────────────────────────┐
│ Workflow: Warranty Claim Process     [Edit]   │
├────────────────────────────────────────────────┤
│                                                │
│ Parent Statuses:                               │
│                                                │
│ 1. ▣ New                     🟦 [Edit] [Delete]│
│    → Can transition to: Job Created, Closed   │
│                                                │
│ 2. ▣ Job Created             🟩 [Edit] [Delete]│
│    → Can transition to: Pending Auth, Closed  │
│    Sub-statuses:                               │
│      • Additional info required               │
│      • Reply received                          │
│                                                │
│ 3. ▣ Pending Manufacturer Auth 🟨 [Edit] [Del] │
│    → Can transition to: Pending Schedule      │
│    Sub-statuses:                               │
│      • Sent to manufacturer                   │
│      • Reply received                          │
│      • Photos requested                        │
│                                                │
│ [+ Add Parent Status]                          │
│                                                │
└────────────────────────────────────────────────┘
```

**Rule Configuration:**
```
┌────────────────────────────────────────────────┐
│ Auto-Assignment Rules                          │
├────────────────────────────────────────────────┤
│                                                │
│ Rule 1: When Status = "Pending QC Review"     │
│   ↳ Assign to: QC Team                        │
│   ↳ Priority: High                             │
│   ↳ Notify: Yes                                │
│   [Edit] [Delete]                              │
│                                                │
│ Rule 2: When Asset Type = "Forklift"          │
│   ↳ Assign to: John Smith                     │
│   [Edit] [Delete]                              │
│                                                │
│ [+ Add Rule]                                   │
│                                                │
└────────────────────────────────────────────────┘
```

##### **Visual Workflow Builder UI (Phase 2)**

**Canvas View:**
```
┌──────────────────────────────────────────────────────┐
│ File Edit View  [Save] [Publish] [Test]             │
├──────────────────────────────────────────────────────┤
│ ┌──────────┐                                         │
│ │  Toolbox │                                         │
│ ├──────────┤                     Canvas              │
│ │ 📋 Status│     ┌─────────┐                         │
│ │ ◆ Decision    │   New   │                         │
│ │ ⚡ Action │    │  Ticket │                         │
│ │ ⏱ Delay  │    └────┬────┘                         │
│ │ ✓ Approval         │                               │
│ │ 🔔 Notify │         ▼                               │
│ │ 🔗 API    │    ┌────────────┐                      │
│ │ {} Script │    │ Warranty?  │◆ (Decision)          │
│ └──────────┘    └──┬─────┬───┘                      │
│                     │ Yes │ No                        │
│                     ▼     ▼                           │
│              ┌──────┐   ┌────────┐                   │
│              │Create│   │ Quote  │                   │
│              │Claim │   │Required│                   │
│              └──────┘   └────────┘                   │
│                                                       │
│                                                       │
│                     [Zoom: 100%] [Grid: On]          │
└──────────────────────────────────────────────────────┘
```

**Node Configuration Panel:**
```
┌────────────────────────────────────────┐
│ Decision Node: Check Warranty         │
├────────────────────────────────────────┤
│                                        │
│ Condition:                             │
│ IF [warranty_active] [=] [true]        │
│                                        │
│ AND [warranty_value] [>] [£ 1000]      │
│                                        │
│ [+ Add Condition]                      │
│                                        │
│ ─────────────────────────────          │
│                                        │
│ True Path: → Warranty Process          │
│ False Path: → Chargeable Process       │
│                                        │
│            [Save] [Cancel]             │
└────────────────────────────────────────┘
```

---

#### **Database Schema for Workflows**

**Tables Required:**
- `workflows` - Workflow definitions
- `workflow_statuses` - Parent statuses per workflow
- `workflow_sub_statuses` - Sub-statuses
- `workflow_transitions` - Allowed status transitions
- `workflow_rules` - Auto-assignment/notification rules
- `workflow_conditions` - If-then-else conditions
- `workflow_actions` - Automated actions
- `workflow_variables` - Custom variables
- `workflow_versions` - Version history
- `workflow_analytics` - Performance metrics

---

#### **Implementation Phases**

**Phase 1: Basic Configuration (Weeks 15-17)**
- Status and sub-status management
- Transition rules
- Auto-assignment rules
- SLA timers
- Notification configuration
- Required fields per status
- Approval thresholds

**Phase 2: Advanced Visual Builder (Weeks 30-35 - Future)**
- Visual drag-drop designer
- Conditional logic engine
- Action nodes and automation
- Multi-path workflows
- Time-based triggers
- Integration builder
- Workflow testing and analytics

---

### **18. Enhanced Activity Logging & Auditing**
**Comprehensive audit trails for compliance and security**

#### **User Activity Logging**

**Automatic Logging of All Actions:**
- User login/logout (with IP address, device, browser)
- Failed login attempts (security monitoring)
- Password changes
- Two-factor authentication events
- Profile updates
- Permission changes

**Business Activity Logging:**
- Ticket creation, updates, status changes
- Job assignments and completions
- Quote creation, approvals, customer acceptance
- Invoice generation and payment updates
- Parts usage and inventory changes
- Purchase order creation and approvals
- Warranty claim submissions
- Asset updates and maintenance
- Customer data changes
- Email sent/received

**System Activity:**
- Configuration changes
- Workflow modifications
- User role changes
- Integration API calls
- Scheduled job executions
- Backup operations

#### **Audit Trail Details**

**Each Log Entry Captures:**
- **Who:** User ID, name, role
- **What:** Action performed, entity affected
- **When:** Timestamp (with timezone)
- **Where:** IP address, location (optional)
- **Why:** Reason/notes (if required)
- **Before/After:** Old and new values (data changes)
- **Context:** Related records, parent tickets, workflows

**Example Audit Entry:**
```
User: John Smith (ID: 123, Role: Engineer)
Action: Updated Ticket Status
Ticket: #12345 "LGMG AR14J Breakdown"
Changed: Status from "Pending Schedule" to "Scheduled"
Timestamp: 2025-10-31 14:23:45 UTC
IP Address: 192.168.1.100
Location: London Office
Reason: Parts arrived, customer notified
Related: Job #789 created, Engineer assigned: Joe Bloggs
```

#### **Audit Log Search & Filtering**

**Search Capabilities:**
- By user (who did what)
- By entity (all changes to ticket #12345)
- By action type (all status changes)
- By date range
- By IP address (security investigation)
- Full-text search across logs

**Advanced Filters:**
- Failed actions only (security monitoring)
- High-risk actions (deletions, permission changes)
- Specific workflow changes
- Customer data access (GDPR compliance)
- Financial transactions

#### **Audit Reports**

**Pre-built Reports:**
1. **User Activity Report**
   - Actions per user per day/week/month
   - Login patterns and anomalies
   - Permission usage tracking

2. **Security Report**
   - Failed login attempts
   - After-hours access
   - Unusual activity patterns
   - Multiple IP addresses per user

3. **Compliance Report**
   - Customer data access log (GDPR Article 30)
   - Financial transaction audit trail
   - Warranty claim documentation trail
   - Configuration change history

4. **Data Change Report**
   - All changes to specific records
   - Who modified customer information
   - Price changes and approvals
   - Inventory adjustments

5. **System Health Report**
   - Failed jobs and errors
   - API rate limit hits
   - Integration failures

#### **Compliance Features**

**GDPR Compliance:**
- Log retention policies (configurable, e.g., 7 years)
- Right to access (export user's audit log)
- Data processing records (Article 30)
- Data breach detection (unusual access patterns)

**ISO 27001 / SOC 2:**
- Immutable audit logs (cannot be edited/deleted)
- Cryptographic integrity (hash verification)
- Tamper detection
- Segregation of duties tracking

**Financial Audit (SOX):**
- Complete financial transaction trail
- Approval workflow documentation
- Price change authorization
- Invoice and payment tracking

#### **Security Monitoring**

**Real-time Alerts:**
- Multiple failed login attempts
- Access from new IP/location
- Bulk data export
- Permission escalation
- After-hours admin actions
- Deletion of critical records

**Anomaly Detection:**
- Unusual activity patterns (ML-based, optional)
- Deviation from normal behavior
- Potential insider threats
- Compromised account detection

#### **Audit Log Management**

**Log Retention:**
- Configurable retention periods per log type
- Automatic archival to cold storage
- Compliant deletion after retention period
- Legal hold capability (prevent deletion)

**Log Storage:**
- Separate audit database (isolation)
- Write-once storage (immutability)
- Encrypted at rest
- Off-site backup

**Log Access Control:**
- Only auditors and admins can view
- Audit log access itself is logged
- Export requires approval
- Watermarked exports (prevent sharing)

#### **Audit Dashboard**

**Real-time Monitoring:**
- Live activity feed
- Failed action alerts
- User session tracking
- Geographic access map (optional)

**Widgets:**
- Active users (real-time)
- Recent high-risk actions
- Failed login attempts (last 24 hours)
- Top users by activity
- System health indicators

---

### **19. Centralized Settings Management**
**Single location for all system, team, and user configuration**

#### **System-Wide Settings**

**General Settings:**
- Application name and tagline
- Default timezone
- Default currency
- Date/time format preferences
- Week start day (Sunday/Monday)
- Business hours definition

**Email Settings:**
- SMTP configuration
- Default from address/name
- Email signature templates
- Bounce handling
- Email rate limiting

**Notification Settings:**
- Enable/disable notification types
- Default notification channels
- Quiet hours (no notifications)
- Batch digest settings

**Integration Settings:**
- API keys for external services
- Webhook URLs
- OAuth credentials
- Third-party service toggles

**Security Settings:**
- Password complexity rules
- Session timeout duration
- Two-factor authentication requirements
- IP whitelist/blacklist
- API rate limits

**Performance Settings:**
- Cache duration
- Queue worker count
- Job timeout limits
- Database query timeout
- File upload size limits

#### **Team-Specific Settings**

**White-Label Branding:**
- Team logo upload
- Primary brand color
- Secondary color
- Custom domain (optional)
- Email template customization

**Operational Settings:**
- Working hours per team
- Holiday calendar
- SLA definitions
- Approval thresholds
- Default workflow templates

**Regional Settings:**
- Team timezone
- Currency override
- Language preference
- Tax settings
- Address format

**Feature Access:**
- Module enable/disable per team
- Advanced features toggle
- Integration access
- API access level

#### **User Preferences**

**Appearance:**
- Light/Dark mode
- Sidebar collapsed state
- Dashboard layout
- Table density (compact/comfortable/spacious)
- Font size

**Notifications:**
- Email notification preferences
- Push notification toggle
- SMS notifications (if enabled)
- In-app notification sound
- Desktop notification permissions

**Workflow Preferences:**
- Default view (Table/Card/List/Kanban)
- Items per page
- Saved filters
- Quick actions customization
- Keyboard shortcuts

**Privacy:**
- Activity visibility
- Profile visibility
- Location sharing (for mobile app)

#### **Feature Flags**

**Gradual Feature Rollout:**
- Enable features for specific teams
- A/B testing capabilities
- Beta feature opt-in
- Feature deprecation warnings

**Example Feature Flags:**
- `advanced_workflow_builder` (Phase 2)
- `ml_suggestions` (predictive features)
- `external_integrations` (per team)
- `mobile_app_access` (per user)

#### **Settings UI**

**Organized Navigation:**
```
Settings
├── General
│   ├── Application
│   ├── Regional
│   └── Business Hours
├── Users & Teams
│   ├── User Management
│   ├── Roles & Permissions
│   └── Team Settings
├── Communication
│   ├── Email Configuration
│   ├── SMS Settings
│   └── Notification Rules
├── Integrations
│   ├── API Keys
│   ├── Webhooks
│   └── Connected Services
├── Security
│   ├── Authentication
│   ├── Access Control
│   └── Audit Settings
├── Workflows
│   ├── Workflow Templates
│   ├── Status Configuration
│   └── Approval Rules
├── Compliance
│   ├── Policies
│   ├── Cookie Consent
│   └── Data Export
├── System
│   ├── Performance
│   ├── Backup Settings
│   └── Maintenance Mode
└── Billing (if applicable)
    ├── Subscription
    └── Payment Methods
```

#### **Configuration Import/Export**

**Export Settings:**
- Export full configuration as JSON
- Export specific sections
- Include/exclude sensitive data (API keys)
- Timestamped backup file

**Import Settings:**
- Upload configuration file
- Preview changes before applying
- Merge or replace strategy
- Validation and error checking
- Rollback capability

**Use Cases:**
- Migrate settings between environments (staging → production)
- Clone team settings for new teams
- Backup before major changes
- Share configuration templates

#### **Settings Version Control**

**Change Tracking:**
- Log all settings changes
- Who changed what and when
- Before/after values
- Reason for change (optional)

**Version History:**
- View previous configurations
- Compare versions (diff view)
- Restore previous version
- Audit trail integration

#### **Settings Validation**

**Input Validation:**
- Type checking (string, number, email, URL)
- Range validation (min/max values)
- Format validation (regex)
- Dependency checking (if A enabled, B required)

**Health Checks:**
- Test email configuration (send test email)
- Validate API keys (test connection)
- Check webhook URLs (ping test)
- Database connection test

---

### **20. Backup & Disaster Recovery**
**Automated backups with monitoring and restoration**

#### **Automated Backup System**

**Backup Types:**
1. **Full Backup**
   - Complete database dump
   - All uploaded files and documents
   - Configuration files
   - Recommended: Weekly

2. **Incremental Backup**
   - Only changed data since last backup
   - Faster and smaller
   - Recommended: Daily

3. **Differential Backup**
   - Changes since last full backup
   - Balance between full and incremental
   - Recommended: Daily

**Backup Scheduling:**
- Flexible cron-based scheduling
- Multiple backup jobs with different schedules
- Off-peak hours preferred (2-4 AM)
- Timezone-aware scheduling

**Example Schedule:**
```
Full Backup:    Every Sunday at 2:00 AM
Incremental:    Daily at 3:00 AM (except Sunday)
Database only:  Every 6 hours (critical data)
Files only:     Daily at 4:00 AM
```

#### **What Gets Backed Up**

**Database:**
- All tables (MySQL dump)
- Stored procedures
- Triggers and views
- User permissions

**Files:**
- Uploaded documents (tickets, jobs, assets)
- Email attachments
- User profile photos
- Team logos and branding
- Generated PDFs (invoices, quotes, reports)

**Configuration:**
- `.env` file (sensitive data encrypted)
- Workflow configurations
- Settings (exported as JSON)
- Custom email templates

**Application Code (optional):**
- For disaster recovery scenarios
- Git commit reference

#### **Backup Storage**

**Local Storage:**
- Temporary local storage before off-site transfer
- Fast restoration option
- Retention: 7 days

**Off-site Storage Options:**
1. **AWS S3** (recommended)
   - Encrypted at rest (AES-256)
   - Versioning enabled
   - Lifecycle policies (auto-delete old backups)
   - Cross-region replication

2. **Google Cloud Storage**
   - Similar features to S3
   - Nearline/Coldline for cost savings

3. **Azure Blob Storage**
   - Alternative cloud option

4. **Self-hosted Off-site**
   - SFTP server
   - NAS device
   - Secondary data center

**Encryption:**
- Encrypt before upload (AES-256)
- Separate encryption keys
- Key management (AWS KMS, HashiCorp Vault)
- Keys never stored with backups

#### **Backup Monitoring**

**Health Checks:**
- Verify backup completion
- Check backup file integrity (checksums)
- Confirm off-site upload success
- Test file count and size consistency

**Alerts:**
- **Critical:** Backup failed
- **Warning:** Backup took longer than expected
- **Info:** Backup completed successfully

**Alert Channels:**
- Email to admins
- SMS for critical failures
- Slack/Teams webhook
- PagerDuty integration (optional)

**Dashboard Widget:**
```
┌────────────────────────────────┐
│ Backup Status                  │
├────────────────────────────────┤
│ Last Full Backup:              │
│   ✅ Oct 31, 2025 2:15 AM      │
│   Size: 2.4 GB                 │
│                                │
│ Last Incremental:              │
│   ✅ Today 3:12 AM             │
│   Size: 145 MB                 │
│                                │
│ Next Scheduled:                │
│   Full: Nov 7, 2:00 AM         │
│   Incremental: Tomorrow 3:00AM │
│                                │
│ Off-site Storage:              │
│   AWS S3: 45 backups (125 GB)  │
│                                │
│ [View All] [Restore] [Test]    │
└────────────────────────────────┘
```

#### **Backup Retention Policy**

**Retention Rules:**
- Daily backups: Keep 7 days
- Weekly full backups: Keep 4 weeks
- Monthly backups: Keep 12 months
- Annual backups: Keep 7 years (compliance)

**Configurable per Team:**
- Different retention for different data types
- Legal hold capability
- Compliant with data regulations

**Automatic Cleanup:**
- Delete expired backups
- Confirm deletion logged in audit trail
- Prevent accidental deletion of recent backups

#### **Backup Restoration**

**Restoration Options:**
1. **Full System Restore**
   - Complete disaster recovery
   - Restore to new server
   - DNS/domain reconfiguration

2. **Partial Restore**
   - Restore specific tables
   - Restore specific files/folders
   - Point-in-time recovery

3. **Single Record Restore**
   - Restore deleted ticket
   - Recover deleted customer
   - Rollback changes

**Restoration Process:**
```
1. Select backup to restore from
2. Choose restoration type (full/partial/single)
3. Preview what will be restored
4. Confirm restoration (requires admin approval)
5. System creates pre-restoration snapshot
6. Restoration executed
7. Verification and testing
8. Notification sent to admins
```

**Restoration UI:**
```
┌────────────────────────────────────────┐
│ Restore Backup                         │
├────────────────────────────────────────┤
│ Select Backup:                         │
│ ○ Full Backup - Oct 31, 2025 2:15 AM  │
│ ○ Full Backup - Oct 24, 2025 2:10 AM  │
│ ○ Full Backup - Oct 17, 2025 2:08 AM  │
│                                        │
│ Restore Type:                          │
│ ○ Full System Restore                 │
│ ○ Database Only                        │
│ ○ Files Only                           │
│ ○ Specific Tables: [Select]           │
│                                        │
│ ⚠️  WARNING:                           │
│ This will overwrite current data.     │
│ A snapshot will be created first.     │
│                                        │
│ Admin Approval Required                │
│ Password: [___________]                │
│                                        │
│        [Cancel]  [Restore]             │
└────────────────────────────────────────┘
```

#### **Backup Testing**

**Automated Test Restoration:**
- Monthly automatic restore to test environment
- Verify data integrity
- Test restoration procedures
- Ensure backups are valid

**Manual Testing:**
- One-click test restore
- Non-destructive (isolated environment)
- Generate test report

#### **Disaster Recovery Plan**

**RTO (Recovery Time Objective):**
- Target: < 4 hours from incident to operational

**RPO (Recovery Point Objective):**
- Target: < 24 hours of data loss maximum
- Critical data: < 6 hours (with 6-hourly backups)

**DR Runbook:**
1. Identify disaster scenario
2. Notify stakeholders
3. Spin up new infrastructure (if needed)
4. Restore latest backup
5. Update DNS/configuration
6. Verify system functionality
7. Resume operations
8. Post-incident review

---

### **21. Compliance & Policy Management**
**Policies, cookie consent, GDPR, and data portability**

#### **Company Policy Management**

**Policy Types:**
1. **Anti-Bribery & Corruption Policy**
2. **Privacy Policy**
3. **Equal Opportunities Policy**
4. **Data Protection Policy (GDPR)**
5. **Acceptable Use Policy**
6. **Code of Conduct**
7. **Whistleblower Policy**
8. **Conflicts of Interest Policy**
9. **Health & Safety Policy**
10. **Information Security Policy**

**Policy Features:**
- Rich text editor for policy content
- Version control (track changes)
- Effective date and review date
- Approval workflow
- Publish/unpublish toggle
- Public visibility (external policies)
- Staff-only policies

**Policy Acceptance Tracking:**
- Require user acknowledgment
- Track who accepted when
- Mandate acceptance on login (if policy updated)
- Audit trail of acceptances
- Report non-compliant users

**Example Policy Acceptance Flow:**
```
User logs in
  ↓
System checks: Policy updated since last acceptance?
  ↓ Yes
Display policy modal
  ↓
User must scroll to bottom
  ↓
"I have read and agree" checkbox
  ↓
[Accept] button enabled
  ↓
Log acceptance in database
  ↓
User can proceed
```

#### **Cookie Consent Management**

**Advanced Cookie Banner:**
- GDPR/CCPA compliant
- Granular consent options
- Remember consent choice
- Easy to revoke consent

**Cookie Categories:**
1. **Strictly Necessary** (cannot be disabled)
   - Authentication cookies
   - Session management
   - Security features
   - CSRF protection

2. **Functional** (optional)
   - User preferences
   - Language selection
   - Theme choice
   - Saved filters

3. **Analytics** (optional)
   - Google Analytics
   - Visitor statistics
   - Performance monitoring
   - Heatmaps

4. **Marketing** (optional)
   - Social media pixels
   - Remarketing
   - Conversion tracking

**Cookie Banner UI:**
```
┌────────────────────────────────────────────┐
│ 🍪 We Value Your Privacy                   │
├────────────────────────────────────────────┤
│ We use cookies to enhance your experience. │
│                                            │
│ ☑ Strictly Necessary (required)           │
│ ☐ Functional                               │
│ ☐ Analytics                                │
│ ☐ Marketing                                │
│                                            │
│ [Reject All] [Accept Selected] [Accept All]│
│                                            │
│ [Cookie Policy] [Manage Preferences]       │
└────────────────────────────────────────────┘
```

**Cookie Management Page:**
- View all cookies used by the site
- Cookie name, purpose, duration
- Provider information
- Enable/disable per category
- Export cookie data (GDPR)

**Consent Log:**
- Track user consent choices
- Timestamp of consent
- IP address and device
- Consent version accepted
- Revocation history

#### **GDPR Compliance Features**

**Right to Access (Article 15):**
- Users request their data
- System generates complete data export
- Includes: profile, tickets, jobs, communications
- Format: JSON + human-readable PDF
- Delivered within 30 days (automated)

**Right to Rectification (Article 16):**
- Users can update their information
- Request data correction
- Admin review and approval

**Right to Erasure / "Right to be Forgotten" (Article 17):**
- User requests account deletion
- Admin review required
- Anonymize data (keep for audit/legal)
- Delete personal identifiable information
- Retain business transaction data (invoices, jobs)
- Log deletion in audit trail

**Right to Data Portability (Article 20):**
- Export data in machine-readable format (JSON, CSV, XML)
- Transfer to another service
- Include all user-generated content

**Right to Object (Article 21):**
- Opt-out of marketing communications
- Object to automated decision-making
- Object to profiling

**Data Processing Records (Article 30):**
- Log all data processing activities
- Purpose of processing
- Categories of data
- Recipients of data
- International transfers
- Retention periods
- Security measures

#### **Data Export/Import System**

**Export Capabilities:**

**1. User Data Export**
- Profile information
- Activity history
- Created tickets and jobs
- Communications (emails, notes)
- Uploaded files
- Settings and preferences

**Formats:**
- JSON (machine-readable)
- CSV (spreadsheet-compatible)
- XML (structured data)
- PDF (human-readable report)
- ZIP archive (all files included)

**2. Team Data Export**
- All team data
- Users and permissions
- Workflows and configurations
- Financial data (invoices, quotes)
- Asset inventory
- Customer database

**3. System Data Export**
- Configuration backup
- Workflow definitions
- Email templates
- Custom fields

**Export UI:**
```
┌────────────────────────────────────────┐
│ Export Your Data                       │
├────────────────────────────────────────┤
│ What to export:                        │
│ ☑ Profile and account info            │
│ ☑ Tickets and communications          │
│ ☑ Job history                          │
│ ☑ Uploaded files                       │
│ ☐ Activity logs                        │
│                                        │
│ Export format:                         │
│ ○ JSON  ○ CSV  ○ XML  ○ PDF  ○ All    │
│                                        │
│ Date range:                            │
│ [All Time ▼]  or  [From] - [To]       │
│                                        │
│ Email when ready:                      │
│ [your@email.com]                       │
│                                        │
│ ⓘ Large exports may take a few minutes│
│                                        │
│         [Cancel]  [Export]             │
└────────────────────────────────────────┘
```

**Import Capabilities:**

**1. User Data Import**
- Onboard users from CSV
- Import customer database
- Bulk asset import
- Historical data migration

**2. Configuration Import**
- Restore settings
- Clone workflows from another team
- Import email templates

**Import Validation:**
- File format verification
- Data type validation
- Duplicate detection
- Error reporting
- Preview before import

**Import UI:**
```
┌────────────────────────────────────────┐
│ Import Data                            │
├────────────────────────────────────────┤
│ Import type:                           │
│ ○ Customers                            │
│ ○ Assets                               │
│ ○ Parts/Inventory                      │
│ ○ Users                                │
│ ○ Configuration                        │
│                                        │
│ Upload file:                           │
│ [Choose File] customers.csv            │
│                                        │
│ Format:  CSV ✓                         │
│ Encoding: UTF-8 ✓                      │
│ Rows: 1,234                            │
│                                        │
│ Options:                               │
│ ☑ Skip duplicates                      │
│ ☐ Update existing records              │
│ ☑ Validate before import               │
│                                        │
│ [Preview] [Cancel] [Import]            │
└────────────────────────────────────────┘
```

#### **Compliance Reporting**

**Pre-built Reports:**
1. **Data Processing Activity Report**
   - All data processing operations
   - Purpose and legal basis
   - Data categories processed

2. **Consent Audit Report**
   - Cookie consent acceptance rates
   - Policy acknowledgments
   - Marketing opt-ins/opt-outs

3. **Data Breach Detection Report**
   - Unusual data access patterns
   - Bulk exports
   - Failed access attempts

4. **Retention Compliance Report**
   - Data past retention period
   - Scheduled deletions
   - Overdue deletions

5. **Access Request Report**
   - GDPR requests received
   - Processing time
   - Status of requests

#### **Data Retention Management**

**Retention Policies:**
- Configure retention period per data type
- Customer data: 7 years after last interaction
- Financial data: 7 years (legal requirement)
- Audit logs: 7 years
- Marketing data: 2 years
- Temporary files: 30 days

**Automatic Data Cleanup:**
- Daily job checks retention rules
- Mark data for deletion
- Grace period before permanent deletion
- Legal hold override (prevent deletion)
- Anonymization instead of deletion (for statistics)

**Data Deletion Confirmation:**
```
┌────────────────────────────────────────┐
│ ⚠️  Data Retention Notice              │
├────────────────────────────────────────┤
│ The following data is scheduled for    │
│ deletion (past retention period):      │
│                                        │
│ • Customer records: 45                 │
│ • Old tickets: 123                     │
│ • Temporary files: 567                 │
│                                        │
│ Scheduled deletion: Nov 7, 2025        │
│                                        │
│ [Review] [Apply Legal Hold] [Proceed]  │
└────────────────────────────────────────┘
```

---

### **22. Custom Fields System**
**Dynamic custom fields for users, assets, customers, and other entities**

#### **Purpose**
Allow teams to extend core entities (users, customers, assets, tickets, jobs, etc.) with custom fields specific to their business needs without code changes.

#### **Supported Entities**
- Users (staff and customers)
- Customers/Companies
- Assets (all types)
- Tickets
- Jobs/Projects
- Products
- Quotes/Invoices
- Sub-contractors
- Purchase Orders

#### **Field Types**

**Text & Numeric:**
1. **Single Line Text** - Short text input (e.g., Badge Number, Employee ID)
2. **Multi-Line Text** - Textarea (e.g., Special Instructions, Notes)
3. **Rich Text** - WYSIWYG editor (e.g., Detailed Description)
4. **Number** - Integer or decimal (e.g., Credit Limit, Asset Weight)
5. **Currency** - Money field with currency selection (e.g., Budget)
6. **Percentage** - Percentage value (e.g., Discount Rate)

**Selection & Options:**
7. **Dropdown** - Single select from predefined options (e.g., Industry Type)
8. **Multi-Select** - Multiple selections (e.g., Skills, Certifications)
9. **Radio Buttons** - Single choice displayed as radios (e.g., Priority Level)
10. **Checkboxes** - Multiple choices displayed as checkboxes (e.g., Services Required)
11. **Yes/No (Boolean)** - Toggle switch (e.g., VIP Customer, Active)

**Date & Time:**
12. **Date** - Date picker (e.g., Contract Start Date)
13. **Date & Time** - DateTime picker (e.g., Appointment Time)
14. **Date Range** - Start and end date (e.g., Project Duration)
15. **Time** - Time only (e.g., Preferred Contact Time)

**Relational & Reference:**
16. **User Reference** - Select a user (e.g., Account Manager)
17. **Customer Reference** - Link to customer (e.g., Parent Company)
18. **Asset Reference** - Link to asset (e.g., Related Equipment)
19. **Lookup/Foreign Key** - Reference to any entity (e.g., Related Ticket)

**File & Media:**
20. **File Upload** - Single file (e.g., Contract Document)
21. **Multiple Files** - Multiple file uploads (e.g., Certificates)
22. **Image Upload** - Image only with preview (e.g., Profile Photo)
23. **Signature** - Digital signature pad (e.g., Customer Authorization)

**Location & Contact:**
24. **Email** - Email validation (e.g., Alternate Email)
25. **Phone** - Phone number with format (e.g., Emergency Contact)
26. **URL** - Website link (e.g., LinkedIn Profile)
27. **Address** - Full address with components (e.g., Billing Address)
28. **Location/GPS** - Map coordinates (e.g., Site Location)

**Advanced:**
29. **JSON** - Structured data (e.g., API Configuration)
30. **Formula/Calculated** - Auto-calculated based on other fields (e.g., Total = Quantity × Price)
31. **Rating** - Star rating (e.g., Satisfaction Score)
32. **Color Picker** - Color selection (e.g., Brand Color)
33. **Tags** - Free-form tags (e.g., Keywords, Labels)

#### **Field Configuration**

**Basic Settings:**
- Field name (internal identifier)
- Display label (shown to users)
- Field type (from list above)
- Description/help text
- Default value
- Placeholder text
- Required vs optional
- Visible to customers (for customer-facing entities)
- Display order/position

**Validation Rules:**
- Required field
- Minimum/maximum length
- Minimum/maximum value (for numbers)
- Regex pattern matching
- Unique value (no duplicates)
- Email format validation
- URL format validation
- Phone format validation
- Custom validation rules

**Conditional Logic:**
- Show/hide field based on other field values
- Example: Show "Reason for Rejection" only if Status = "Rejected"
- Example: Show "Other (specify)" text field if "Other" selected in dropdown
- Multiple conditions with AND/OR logic

**Permissions:**
- View permission by role (Admin, Manager, Staff, Customer)
- Edit permission by role
- Read-only fields
- Admin-only fields (sensitive data)

**Display Options:**
- Show in list/table views
- Show in detail view
- Show in forms (create/edit)
- Show in customer portal
- Group fields into sections
- Collapsible sections

#### **Field Groups/Sections**

**Purpose:** Organize related custom fields into logical sections

**Example for Assets:**
```
Section: "Compliance Information"
├── Certification Number (Text)
├── Certifying Body (Dropdown)
├── Certification Date (Date)
├── Expiry Date (Date)
└── Certificate Document (File Upload)

Section: "Financial Details"
├── Purchase Price (Currency)
├── Current Value (Currency)
├── Depreciation Rate (Percentage)
└── Insurance Value (Currency)
```

**Section Features:**
- Section name and description
- Collapsible/expandable sections
- Display order
- Conditional visibility (show section based on criteria)
- Icons for visual identification

#### **Pre-built Field Templates**

**Quick-add common field sets:**

**For Customers:**
- Company Details (Industry, Employee Count, Annual Revenue)
- Compliance (ISO Certifications, Safety Ratings)
- Financial (Credit Limit, Payment Terms, Tax ID)
- Contact Preferences (Preferred Contact Method, Best Time to Call)

**For Assets:**
- Equipment Specs (Make, Model, Serial Number, Year)
- Compliance (Certifications, Inspection Dates, Next Service Due)
- Financial (Purchase Price, Depreciation, Insurance)
- Location (Site, Building, Floor, Room)

**For Jobs:**
- Site Access (Access Instructions, Key Code, Contact on Site)
- Safety (RAMS Required, PPE Required, Hazards)
- Billing (Billing Method, PO Number, Charge Code)

**For Users (Staff):**
- Certifications (License Number, Expiry Date, Issuing Body)
- Emergency Contact (Name, Phone, Relationship)
- Skills (Specializations, Languages, Tools)

#### **UI for Managing Custom Fields**

**Admin Interface (Filament):**

```
┌──────────────────────────────────────────────────┐
│ Custom Fields: Assets                            │
├──────────────────────────────────────────────────┤
│                                                  │
│ [+ Add Field]  [+ Add Section]  [Import Template]│
│                                                  │
│ ┌────────────────────────────────────────────┐  │
│ │ Section: General Information (Expanded ▼)  │  │
│ ├────────────────────────────────────────────┤  │
│ │ ≡ Asset Tag Number                         │  │
│ │   Type: Text  |  Required  |  [Edit] [Del] │  │
│ │                                            │  │
│ │ ≡ Purchase Date                            │  │
│ │   Type: Date  |  Optional  |  [Edit] [Del] │  │
│ └────────────────────────────────────────────┘  │
│                                                  │
│ ┌────────────────────────────────────────────┐  │
│ │ Section: Compliance (Collapsed ▶)          │  │
│ └────────────────────────────────────────────┘  │
│                                                  │
│ ┌────────────────────────────────────────────┐  │
│ │ Section: Financial Details (Collapsed ▶)   │  │
│ └────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────┘
```

**Field Editor Modal:**
```
┌──────────────────────────────────────────────┐
│ Edit Custom Field                            │
├──────────────────────────────────────────────┤
│ Display Label: *                             │
│ [Asset Tag Number_____________________]      │
│                                              │
│ Field Type: *                                │
│ [Single Line Text        ▼]                  │
│                                              │
│ Help Text:                                   │
│ [Unique identifier for this asset_____]      │
│                                              │
│ Default Value:                               │
│ [____________________________________]       │
│                                              │
│ ☑ Required field                             │
│ ☑ Show in table views                        │
│ ☐ Read-only after creation                   │
│ ☑ Visible to customers                       │
│                                              │
│ Validation Rules:                            │
│ • Minimum length: [3]   characters           │
│ • Maximum length: [50]  characters           │
│ ☑ Unique value (no duplicates)               │
│                                              │
│ Permissions:                                 │
│ View: [All Roles    ▼]                       │
│ Edit: [Admin, Manager ▼]                     │
│                                              │
│ Conditional Display:                         │
│ Show when: [Asset Type] [equals] [Equipment] │
│ [+ Add Condition]                            │
│                                              │
│         [Cancel]  [Save Field]               │
└──────────────────────────────────────────────┘
```

#### **Frontend Display**

**In Forms (Create/Edit):**
- Custom fields render after standard fields
- Organized by sections
- Dynamic rendering based on field type
- Real-time validation
- Conditional show/hide
- Inline help text/tooltips

**In Detail Views:**
- Display in dedicated "Additional Information" section
- Or integrated with main fields
- Formatted based on field type (dates, currency, etc.)
- Empty fields can be hidden or shown as "Not set"

**In Table/List Views:**
- Optional columns for custom fields
- User can choose which custom fields to show
- Sortable and filterable
- Proper formatting in cells

**In Customer Portal:**
- Only show fields marked "visible to customers"
- Read-only or editable based on permissions
- Same sectioned layout as staff view

#### **Data Storage**

**Option 1: JSON Column (Recommended)**
- Store all custom field values in a JSON column per entity
- Example: `assets.custom_fields` (JSON)
- Pros: Flexible, easy migrations, simple schema
- Cons: Limited querying, indexing challenges

**Option 2: Polymorphic Pivot Table**
- `custom_field_values` table with polymorphic relationship
- Columns: entity_type, entity_id, custom_field_id, value
- Pros: Better querying, indexing, validation
- Cons: More complex, more database rows

**Recommended Approach:** Hybrid
- Simple fields (text, numbers, dates): JSON column
- Relational fields (user references, lookups): Pivot table
- File uploads: Spatie Media Library with custom properties

#### **Search & Filtering**

**Global Search:**
- Index custom fields in Meilisearch
- Include in entity search results
- Weighted relevance (custom fields lower priority than core fields)

**Table Filters:**
- Filter by custom field values
- Support for all field types
- Dropdown filters for select/radio/checkbox fields
- Date range filters for date fields
- Text search for text fields

#### **API Support**

**REST API:**
- Include custom fields in entity responses
- POST/PUT with custom field values
- Validation applied to API requests
- Field definitions available via API endpoint
- Swagger/OpenAPI documentation includes custom fields

#### **Import/Export**

**CSV Import:**
- Map CSV columns to custom fields
- Validation on import
- Error reporting for invalid values
- Preview before import

**CSV Export:**
- Include custom fields in exports
- User selects which custom fields to export
- Proper formatting (dates, currency, etc.)

#### **Audit Trail**

**Track Changes:**
- Log all custom field value changes
- Before/after values in audit log
- Who changed, when, why
- Display in activity feed

#### **Examples of Business Use Cases**

**Manufacturing Company:**
- Assets: Machine Hour Meter, Last Calibration Date, Calibration Certificate
- Customers: Preferred Supplier Number, Credit Terms, Industry Sector

**Healthcare Facility:**
- Assets: Biomedical Device ID, Sterilization Method, FDA Approval Number
- Users: Medical License Number, Specializations, On-Call Schedule

**Property Management:**
- Assets: Building Name, Unit Number, Tenant Name, Lease End Date
- Jobs: Access Code, Parking Instructions, Pet on Premises

**IT Support:**
- Assets: Operating System, Software Licenses, Remote Access ID
- Tickets: Affected Business Process, Estimated Downtime, Recovery Priority

---

### **23. Modular Installation System**
**Flexible module-based architecture with on-demand installation**

#### **Purpose**
Allow teams to install only the modules they need, reducing complexity and improving performance. Modules can be enabled/disabled without affecting core functionality.

#### **Core Modules (Always Active)**
Cannot be disabled:
1. **Authentication & Users** - User management, roles, permissions
2. **Dashboard** - Main dashboard and navigation
3. **Settings** - System configuration
4. **Activity Log** - Audit trail
5. **Teams** - Multi-tenant team management

#### **Optional Modules**

**Customer Management:**
- **CRM & Customers** - Customer database, contacts, organizations
- **Customer Portal** - Customer self-service portal

**Products & Commerce:**
- **Products & Services** - Product catalog
- **E-commerce** - Online storefront and cart
- **Inventory & Parts** - Stock management, parts tracking
- **Purchase Orders** - PO system and supplier management

**Service Delivery:**
- **Quotes** - Quotation system and proposal generation
- **Assets** - Asset tracking and management
- **Ticketing** - Support ticket system
- **Jobs** - Job/task management
- **Projects** - Project management with Gantt/Kanban
- **Timesheets** - Time tracking for engineers

**Communication:**
- **Email Management** - Email-to-ticket automation, IMAP monitoring
- **Blog & Content** - Content management system

**Advanced Features:**
- **Warranty Management** - Warranty tracking and claims
- **Sub-contractors** - External engineer management
- **Workflow Builder** - Custom workflow configuration
- **API** - RESTful API access

**Compliance & Administration:**
- **Audit Logging** - Enhanced activity logging
- **Backup & Recovery** - Automated backup system
- **Compliance & Policies** - GDPR, cookie consent, policies

#### **Module Dependencies**

**Dependency Tree:**
```
Jobs Module requires:
└─ Assets Module (optional but recommended)
└─ Customers Module
└─ Timesheets Module (optional)

Tickets Module requires:
└─ Customers Module
└─ Assets Module (optional but recommended)

Quotes Module requires:
└─ Products & Services Module
└─ Customers Module

Purchase Orders Module requires:
└─ Inventory & Parts Module
└─ Products & Services Module

Warranty Management requires:
└─ Assets Module
└─ Jobs or Tickets Module

Email Management enhances:
└─ Tickets Module (email-to-ticket)
└─ Jobs Module (email notifications)
```

#### **Module Installation Process**

**Installation Wizard:**
```
┌────────────────────────────────────────────┐
│ Install Module: Asset Management          │
├────────────────────────────────────────────┤
│ ℹ️ This module enables:                    │
│ • Asset registry and tracking             │
│ • Maintenance schedules                   │
│ • Compliance monitoring                   │
│ • Expiry date alerts                      │
│                                           │
│ ⚠️ Dependencies:                           │
│ ✓ Customers Module (installed)            │
│                                           │
│ 📊 Database:                               │
│ • Will create 8 new tables                │
│ • Estimated size: ~50 MB for 1000 assets  │
│                                           │
│ 👥 Permissions:                            │
│ • New permissions will be added:          │
│   - view_assets                           │
│   - create_assets                         │
│   - edit_assets                           │
│   - delete_assets                         │
│                                           │
│ ⏱️ Installation time: ~30 seconds          │
│                                           │
│ [Cancel]            [Install Module]      │
└────────────────────────────────────────────┘
```

**Installation Steps (Automated):**
1. **Dependency Check** - Verify required modules installed
2. **Database Migration** - Create module-specific tables
3. **Seeder Execution** - Install default data (statuses, templates, etc.)
4. **Permission Creation** - Add module permissions to roles
5. **Navigation Update** - Add menu items to sidebar
6. **Settings Initialization** - Create module settings with defaults
7. **Cache Clear** - Refresh application cache
8. **Success Confirmation** - Show completion message

#### **Module Management Interface**

**Installed Modules View:**
```
┌────────────────────────────────────────────────────────┐
│ Modules                                 [+ Install New]│
├────────────────────────────────────────────────────────┤
│                                                        │
│ Core Modules (Always Active)                          │
│ ┌──────────────────────────────────────────────────┐  │
│ │ ⚙️ Authentication & Users         [Core Module]  │  │
│ │ 🏠 Dashboard                       [Core Module]  │  │
│ │ 📊 Activity Log                    [Core Module]  │  │
│ └──────────────────────────────────────────────────┘  │
│                                                        │
│ Installed Modules                                      │
│ ┌──────────────────────────────────────────────────┐  │
│ │ 👥 CRM & Customers                              │  │
│ │    Installed: Jan 15, 2025  •  Version 1.0     │  │
│ │    [⚙️ Configure] [📖 Documentation] [Disable]   │  │
│ └──────────────────────────────────────────────────┘  │
│ ┌──────────────────────────────────────────────────┐  │
│ │ 📦 Assets Management                            │  │
│ │    Installed: Jan 20, 2025  •  Version 1.0     │  │
│ │    [⚙️ Configure] [📖 Documentation] [Disable]   │  │
│ └──────────────────────────────────────────────────┘  │
│                                                        │
│ Available Modules (Not Installed)                      │
│ ┌──────────────────────────────────────────────────┐  │
│ │ 🎫 Ticketing System                             │  │
│ │    Enable support ticket management             │  │
│ │    Dependencies: Customers Module ✓             │  │
│ │                           [+ Install]           │  │
│ └──────────────────────────────────────────────────┘  │
│ ┌──────────────────────────────────────────────────┐  │
│ │ 🔨 Jobs & Projects                              │  │
│ │    Job and project management with Kanban      │  │
│ │    Dependencies: Customers ✓, Assets (opt)     │  │
│ │                           [+ Install]           │  │
│ └──────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────┘
```

#### **Module Disable/Uninstall**

**Disable vs Uninstall:**

**Disable:**
- Module functionality hidden from UI
- Database tables remain intact
- Data preserved
- Can be re-enabled instantly
- Settings preserved

**Uninstall:**
- Removes module completely
- Drops database tables
- **Data permanently deleted**
- Requires confirmation
- Cannot be undone without backup

**Disable Confirmation:**
```
┌────────────────────────────────────────────┐
│ ⚠️ Disable Module: Asset Management        │
├────────────────────────────────────────────┤
│ This will:                                 │
│ ✓ Hide module from navigation              │
│ ✓ Disable module functionality             │
│ ✓ Preserve all data                        │
│                                           │
│ ⚠️ Impact:                                  │
│ • 3 active jobs reference assets          │
│ • 12 tickets linked to assets             │
│ • These will lose asset data visibility   │
│                                           │
│ You can re-enable this module anytime.    │
│                                           │
│ [Cancel]            [Disable Module]      │
└────────────────────────────────────────────┘
```

**Uninstall Confirmation (More Severe):**
```
┌────────────────────────────────────────────┐
│ 🗑️ UNINSTALL Module: Asset Management      │
├────────────────────────────────────────────┤
│ ⚠️ WARNING: This action is IRREVERSIBLE    │
│                                           │
│ This will PERMANENTLY DELETE:              │
│ • 245 assets                              │
│ • 1,023 maintenance records               │
│ • 56 service schedules                    │
│ • All asset-related data                  │
│                                           │
│ ⚠️ Impact on other modules:                │
│ • 3 active jobs will lose asset links     │
│ • 12 tickets will lose asset references   │
│                                           │
│ ✓ Database backup created before uninstall│
│                                           │
│ Type "DELETE ASSETS" to confirm:          │
│ [____________________________________]     │
│                                           │
│ [Cancel]            [Uninstall Module]    │
└────────────────────────────────────────────┘
```

#### **Module Configuration**

**Per-Module Settings:**
Each module has its own settings page:
- Feature toggles within module
- Default values and behaviors
- Integration settings
- Permissions and access control
- Notification preferences

**Example: Assets Module Settings:**
- Enable/disable maintenance schedules
- Default notification thresholds (7/14/30 days)
- Auto-create jobs for due services
- Asset numbering format
- Required fields for asset creation

#### **Navigation & UI Adaptation**

**Dynamic Navigation:**
- Sidebar menu items added/removed based on installed modules
- Dashboard widgets only show for installed modules
- Global search includes/excludes based on modules
- Breadcrumbs adapt to available modules

**Feature Flags Integration:**
- Modules work with feature flags
- Can enable module for specific teams only
- Gradual rollout of new modules
- A/B testing module features

#### **Performance Optimization**

**Benefits:**
- Smaller database schema (only tables for installed modules)
- Faster queries (fewer joins)
- Reduced memory usage
- Lighter JavaScript bundle (module-specific assets)
- Faster page loads
- Simplified permissions matrix

#### **Module Marketplace (Future)**

**Vision for Extension Ecosystem:**
- First-party modules (official)
- Third-party modules (community)
- Module ratings and reviews
- Version compatibility checking
- Automatic updates
- Paid premium modules

---

### **24. Maintenance Mode**
**Flexible maintenance mode with granular access control**

#### **Purpose**
Provide controlled system downtime for maintenance, updates, or troubleshooting while allowing specific access levels for administrators and critical operations.

#### **Maintenance Modes**

**1. Fully Available (Normal Operation)**
- System fully operational
- All users can access all features
- Default state

**2. Frontend Maintenance (Backend Only)**
- Public-facing pages disabled
- Customer portal disabled
- Staff backend fully accessible
- API remains available
- Background jobs continue processing

**Use Cases:**
- Frontend deployment/updates
- Customer portal maintenance
- Public website changes

**3. Frontend + Customer Login Disabled**
- All public pages disabled
- Customer login disabled
- Existing customer sessions terminated
- Staff can still log in and work
- API available with authentication
- Background jobs continue

**Use Cases:**
- Customer-facing feature updates
- Database maintenance affecting customer data
- Security updates requiring customer re-authentication

**4. Staff-Only Mode (Critical Maintenance)**
- Only administrators and selected staff can access
- All customers blocked
- Most staff users blocked
- Only whitelisted users allowed
- API limited to whitelisted tokens
- Background jobs paused (optional)

**Use Cases:**
- Major database migrations
- Critical security patches
- System-wide configuration changes

**5. Specific Staff Members Only (Emergency Mode)**
- Only specifically selected users allowed
- All other access completely blocked
- Select users by ID or email
- Emergency troubleshooting access
- All automated processes paused

**Use Cases:**
- Emergency troubleshooting
- Data corruption investigation
- Critical hotfixes

**6. Completely Offline (Full Maintenance)**
- No one can access (except via SSH/console)
- All web traffic shows maintenance page
- All background jobs paused
- API completely unavailable

**Use Cases:**
- Server maintenance
- Major Laravel/PHP version upgrades
- Database engine upgrades

#### **Maintenance Mode Interface**

**Activation Panel:**
```
┌────────────────────────────────────────────────────┐
│ System Maintenance Mode                            │
├────────────────────────────────────────────────────┤
│ Current Status: ● Fully Available                  │
│                                                    │
│ Maintenance Mode: *                                │
│ ○ Fully Available (no restrictions)               │
│ ○ Frontend Maintenance (staff only backend)       │
│ ○ Frontend + Customer Login Disabled              │
│ ● Staff-Only Mode (selected staff + admins)       │
│ ○ Specific Staff Members Only (emergency)         │
│ ○ Completely Offline (full maintenance)           │
│                                                    │
│ ┌──────────────────────────────────────────────┐  │
│ │ Staff-Only Mode Configuration                │  │
│ ├──────────────────────────────────────────────┤  │
│ │ Allowed Roles:                               │  │
│ │ ☑ Administrators (always allowed)            │  │
│ │ ☑ Managers                                   │  │
│ │ ☐ Engineers                                  │  │
│ │ ☐ Sales                                      │  │
│ │                                              │  │
│ │ Additional Whitelisted Users:                │  │
│ │ [+ Add User]                                 │  │
│ │ • John Smith (john@company.com)   [Remove]  │  │
│ │ • Sarah Johnson (sarah@company.com) [Remove]│  │
│ │                                              │  │
│ │ Background Jobs:                             │  │
│ │ ○ Continue running                           │  │
│ │ ● Pause all jobs                             │  │
│ │ ○ Pause except critical jobs                 │  │
│ └──────────────────────────────────────────────┘  │
│                                                    │
│ Maintenance Message:                               │
│ ┌──────────────────────────────────────────────┐  │
│ │ We're performing system maintenance to      │  │
│ │ improve your experience. We'll be back      │  │
│ │ shortly.                                    │  │
│ └──────────────────────────────────────────────┘  │
│                                                    │
│ Scheduled End Time (optional):                     │
│ [2025-11-01] at [03:00] AM  [Use Current Time]    │
│                                                    │
│ Show Countdown Timer on Maintenance Page:          │
│ ☑ Yes, show estimated time remaining               │
│                                                    │
│ Custom CSS/Styling (advanced):                     │
│ [Edit Maintenance Page Template]                   │
│                                                    │
│                  [Cancel]  [Enable Maintenance]    │
└────────────────────────────────────────────────────┘
```

#### **Maintenance Page Design**

**Customer-Facing Maintenance Page:**
```html
┌────────────────────────────────────────────┐
│                                            │
│           🔧 [Company Logo]                │
│                                            │
│      We're Currently Under Maintenance    │
│                                            │
│  We're making improvements to serve you   │
│  better. We'll be back online shortly.    │
│                                            │
│  ⏱️ Estimated time remaining: 23 minutes   │
│                                            │
│  Expected completion: Nov 1, 2025 3:00 AM │
│                                            │
│  ───────────────────────────────────────  │
│                                            │
│  Need urgent assistance?                  │
│  Call: 1-800-123-4567                     │
│  Email: support@company.com               │
│                                            │
│  [Check Status] [Subscribe to Updates]    │
│                                            │
└────────────────────────────────────────────┘
```

**Staff Login During Maintenance:**
```
┌────────────────────────────────────────────┐
│ ⚠️ System in Maintenance Mode              │
├────────────────────────────────────────────┤
│ The system is currently under maintenance. │
│                                            │
│ Mode: Staff-Only Access                    │
│ Started: Nov 1, 2025 2:30 AM               │
│ Expected end: Nov 1, 2025 3:00 AM          │
│                                            │
│ If you have authorized access, you may     │
│ proceed with login.                        │
│                                            │
│ ─────────────────────────────────────────  │
│                                            │
│ Email:                                     │
│ [________________________________]         │
│                                            │
│ Password:                                  │
│ [________________________________]         │
│                                            │
│         [Cancel]  [Login]                  │
└────────────────────────────────────────────┘
```

**Unauthorized Access Attempt:**
```
┌────────────────────────────────────────────┐
│ 🚫 Access Restricted                       │
├────────────────────────────────────────────┤
│ The system is currently in maintenance     │
│ mode and your account does not have        │
│ access at this time.                       │
│                                            │
│ Maintenance Mode: Staff-Only Access        │
│ Expected completion: Nov 1, 2025 3:00 AM   │
│                                            │
│ If you believe this is an error, please    │
│ contact your system administrator.         │
│                                            │
│                     [OK]                   │
└────────────────────────────────────────────┘
```

#### **Access Control Logic**

**Mode-Based Permissions:**

**Frontend Maintenance:**
```php
if (maintenanceMode === 'frontend_only') {
    allow: [
        '/admin/*',      // All admin routes
        '/api/*',        // All API endpoints
    ]
    block: [
        '/',             // Homepage
        '/portal/*',     // Customer portal
        '/blog/*',       // Public blog
    ]
}
```

**Staff-Only Mode:**
```php
if (maintenanceMode === 'staff_only') {
    if (user.role === 'admin') return true;
    if (whitelistedRoles.includes(user.role)) return true;
    if (whitelistedUserIds.includes(user.id)) return true;
    return false;
}
```

**Specific Users Only:**
```php
if (maintenanceMode === 'emergency') {
    if (specificUserIds.includes(user.id)) return true;
    if (specificEmails.includes(user.email)) return true;
    return false;
}
```

#### **Maintenance Mode Features**

**Scheduled Maintenance:**
- Schedule maintenance in advance
- Auto-enable at specified time
- Auto-disable when maintenance complete
- Email notifications before scheduled maintenance
- In-app warnings to logged-in users
- Countdown timer (e.g., "Maintenance starting in 15 minutes")

**Grace Period:**
- Warn users before forcibly logging them out
- Allow active sessions to finish current tasks
- Soft mode: Show banner but allow access for X minutes
- Hard mode: Immediate logout and access restriction

**Emergency Toggle:**
- Quick-enable emergency mode (1-click)
- Pre-configured emergency mode settings
- SMS alerts to administrators
- Bypass confirmation dialogs

**Status Page Integration:**
- Public status page (e.g., status.company.com)
- Real-time maintenance status
- Incident history
- Subscribe to status updates

#### **Notifications**

**Before Maintenance:**
- Email to all users (configurable lead time)
- In-app banner: "Scheduled maintenance in 2 hours"
- SMS to administrators (optional)
- API webhook to integrations

**During Maintenance:**
- Maintenance page to blocked users
- SMS/email to whitelisted users with access details
- Status page updates

**After Maintenance:**
- Email: "System is back online"
- Audit log entry
- Report: Downtime duration, affected users, completed tasks

#### **Audit & Logging**

**Maintenance Log:**
- Who enabled maintenance mode
- Mode type selected
- Start time and end time
- Whitelisted users/roles
- Reason/description
- Who disabled maintenance mode
- Any issues encountered

**User Access Attempts:**
- Log all blocked access attempts during maintenance
- User identity, timestamp, IP address
- Helps identify unauthorized access attempts

#### **API Behavior During Maintenance**

**Frontend Maintenance:**
- API remains fully available
- Normal authentication required
- All endpoints operational

**Staff-Only Maintenance:**
- API requires whitelisted token
- Return `503 Service Unavailable` to non-whitelisted requests
- Include `Retry-After` header with expected completion time

**Full Maintenance:**
- All API requests return `503 Service Unavailable`
- Except health check endpoint: `/api/health`
- Monitoring tools can still check status

#### **Background Jobs During Maintenance**

**Job Behavior Options:**

**Option 1: Continue All Jobs**
- All queues continue processing
- Useful for backend-only maintenance

**Option 2: Pause All Jobs**
- All queues paused
- Jobs resume after maintenance ends
- Useful for database maintenance

**Option 3: Critical Jobs Only**
- Pause non-critical jobs
- Allow critical jobs (e.g., billing, notifications)
- Define critical vs non-critical per job class

**Queue Dashboard:**
- Show paused queues in Horizon
- Manually resume/pause specific queues
- Monitor pending jobs during maintenance

#### **Rollback & Recovery**

**Maintenance Failed:**
- If maintenance fails, auto-rollback
- Restore previous state
- Alert administrators
- Automatically disable maintenance mode

**Stuck in Maintenance:**
- Admin override command via console
- `php artisan maintenance:disable --force`
- Emergency access via secret URL token
- Prevents accidental lockout

#### **Multi-Tenant Considerations**

**Per-Team Maintenance:**
- Enable maintenance for specific teams only
- Other teams unaffected
- Useful for team-specific updates

**White-Label Maintenance Pages:**
- Maintenance page uses team branding
- Team logo and colors
- Customizable message per team

---


## **Key Packages & Libraries**

### **Required Packages**
```
# Core Framework
laravel/jetstream (auth + teams with Livewire stack)
livewire/volt (single-file components)
livewire/livewire (already included with Jetstream)

# UI & Components
wireui/wireui (modern UI components for Livewire)
filament/filament (admin panel + tables)
blade-ui-kit/blade-heroicons (Heroicon components)

# File & Media
spatie/laravel-medialibrary (file uploads + image conversions)
spatie/laravel-pdf (invoice/quote PDF generation)
intervention/image (image manipulation)

# Multi-tenancy & Permissions
spatie/laravel-permission (extend Jetstream roles)

# Activity & Logging
spatie/laravel-activitylog (audit trail)

# Calendar & Scheduling
spatie/laravel-calendar (scheduling events)

# Search
laravel/scout (search abstraction)
meilisearch/meilisearch-php (search engine client)

# Queue & Background Jobs
laravel/horizon (queue monitoring dashboard)
predis/predis (Redis client)

# Real-time
pusher/pusher-php-server (WebSocket for real-time features)

# API
laravel/sanctum (API authentication)

# Development & Code Quality
laravel/pint (code formatting)
barryvdh/laravel-debugbar (development debugging)
```

### **Email Management Packages**
```
# Email Processing
webklex/php-imap (IMAP/POP3 email fetching and parsing)
webklex/laravel-imap (Laravel wrapper for php-imap)
zbateson/mail-mime-parser (parse complex email structures)

# Email Sending & Tracking
symfony/mime (email composition)
```

### **Workflow Builder Packages**
```
# Phase 1: Basic Workflows
# (No additional packages - built with Laravel/Livewire)

# Phase 2: Advanced Visual Workflow Builder (Future)
# Consider these options:
cknow/laravel-money (currency handling in conditions)
mtownsend/read-time (estimate workflow completion time)

# Optional: If using visual node editor
# spatie/laravel-json-api-paginate (API for workflow canvas)
# OR build custom with Livewire + Alpine.js
```

### **Audit Logging & Compliance Packages**
```
# Activity Logging
spatie/laravel-activitylog (already listed - comprehensive audit trails)

# Cookie Consent
spatie/laravel-cookie-consent (GDPR-compliant cookie banner)

# Data Export/Import
maatwebsite/excel (export to Excel/CSV - already listed)
spatie/simple-excel (alternative, simpler Excel export)

# Backup & Recovery
spatie/laravel-backup (already listed - database & files backup)
league/flysystem-aws-s3-v3 (S3 storage for backups)
spatie/db-dumper (database dumps)

# Security & Encryption
pragmarx/google2fa-laravel (two-factor authentication)
spatie/laravel-csp (Content Security Policy headers)

# IP Detection & Geolocation (for audit logs)
stevebauman/location (IP geolocation - optional)
torann/geoip (alternative geolocation package)

# PDF Generation (for reports, policy exports)
spatie/laravel-pdf (already listed - Chromium-based PDF generation)
```

### **Additional Packages to Consider**
```
# Dashboard Customization
asantibanez/livewire-charts (charts for dashboard widgets)
wire-elements/modal (modal dialogs for Livewire)

# Data Export
maatwebsite/excel (export to Excel/CSV)
spatie/laravel-query-builder (API filtering and sorting)

# SEO & Content
spatie/laravel-sluggable (URL-friendly slugs)
artesaos/seotools (meta tags and SEO)

# Storage
league/flysystem-aws-s3-v3 (S3 storage option)

# Backup & Maintenance
spatie/laravel-backup (automated backups)

# Notifications
laravel/slack-notification-channel (Slack notifications)

# PWA Support
silvanite/laravel-pwa (Progressive Web App manifest and service worker)

# Rich Text Editor
tiptap for Livewire (modern WYSIWYG editor)

# Date/Time Handling
nesbot/carbon (already included, but worth noting)

# E-signature
docuseal/docuseal (open-source e-signature solution)
OR
signnow/signnow-php-sdk (commercial e-signature API)
```

---

## **Frontend Tooling & Build**

### **Build System**
- **Vite** (Laravel's default, fast HMR)
- **PostCSS** for CSS processing
- **Autoprefixer** for browser compatibility

### **Tailwind CSS Configuration**
```javascript
// Custom Tailwind config for modern SaaS look
{
  theme: {
    extend: {
      colors: {
        // Dynamic brand colors (injected per team)
        primary: 'var(--color-primary)',
        secondary: 'var(--color-secondary)',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'soft': '0 2px 15px rgba(0, 0, 0, 0.08)',
        'card': '0 1px 3px rgba(0, 0, 0, 0.12)',
      },
      animation: {
        'slide-in': 'slideIn 0.2s ease-out',
        'fade-in': 'fadeIn 0.15s ease-in',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
```

### **Alpine.js Usage**
- Lightweight JavaScript for interactivity
- Dropdown menus and accordions
- Mobile menu toggle
- Modal dialogs (when not using WireUI)
- Form validation feedback
- Tooltip and popovers
- Auto-save indicators

### **Asset Pipeline**
```javascript
// vite.config.js
export default {
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  build: {
    chunkSizeWarningLimit: 1000,
    rollupOptions: {
      output: {
        manualChunks: {
          'vendor': ['alpinejs'],
          'charts': ['chart.js'], // if using charts
        },
      },
    },
  },
}
```

### **JavaScript Libraries (Minimal)**
- **Alpine.js** (included with Livewire)
- **Chart.js** or **ApexCharts** (for dashboard widgets)
- **SortableJS** (for drag-drop dashboard customization)
- **Flatpickr** (date picker, if not using WireUI's)
- **Tiptap** (rich text editor for blog posts, descriptions)

---

## **Infrastructure & DevOps**

### **Deployment**
- **Docker** containers (Nginx, PHP-FPM, MySQL, Redis, Meilisearch)
- **Custom VPS** setup
- Docker Compose for local development
- Laravel Sail for Laravel development environment

### **Email**
- SMTP provider (Mailgun, SendGrid, or similar)
- Email queuing for performance
- Email templates using Blade components
- Notification channels (email, database, real-time)
- Email preview in development (Mailtrap/MailHog)

### **Search**
- **Meilisearch** server (Docker container or cloud)
- Indexed entities: products, assets, tickets, jobs, customers, blog posts
- Typo-tolerant full-text search
- Filters and facets for refinement
- Search analytics and popular queries

### **CI/CD**
- **GitHub Actions** workflows:
  - Run tests on every push/PR
  - Code style checks (Laravel Pint)
  - Static analysis with PHPStan/Larastan (optional)
  - Build assets (Vite production build)
  - Automated deployment to staging/production
  - Database backup before deployment
  - Zero-downtime deployment with Laravel Envoy

### **Testing**
- **Pest** (modern test framework with better DX)
- Feature tests for critical workflows:
  - Quote creation and approval
  - Job assignment and status updates
  - Ticket lifecycle
  - Invoice generation
  - API endpoints
- Browser tests with **Laravel Dusk** for user journeys
- Parallel test execution for speed
- Test database seeding with realistic data
- Code coverage reporting

### **Monitoring & Performance**
- **Laravel Telescope** (development debugging)
- **Laravel Pulse** (production metrics - Laravel 11+)
- Application performance monitoring (New Relic, Sentry, or similar)
- Database query monitoring
- Error tracking and alerting
- Uptime monitoring

---

## **Database Schema (High-Level)**

### **Core Tables**
- `users` - All users (staff + customers)
- `teams` - Multi-tenant teams (companies)
- `team_user` - Pivot table with roles
- `roles` & `permissions` - Extended RBAC

### **E-commerce & Inventory**
- `products` - Product/service catalog (both services and physical parts)
- `product_categories`
- `parts` - Physical parts/components for inventory
  - Fields: sku, name, description, category, supplier_id, cost_price, sell_price, reorder_point, reorder_quantity
- `inventory_locations` - Warehouse, van stock per engineer, etc.
- `inventory` - Stock levels per part per location
  - Fields: part_id, location_id, quantity, last_counted_at
- `inventory_movements` - Stock in/out transactions
  - Fields: part_id, location_id, quantity, type (purchase/job_usage/transfer/adjustment), job_id, user_id
- `suppliers` - Parts suppliers
  - Fields: name, contact, terms, lead_time_days
- `purchase_orders` - Orders to suppliers
  - Fields: supplier_id, status, total, expected_delivery_date

### **Quotations & Invoices**
- `quotes` - Quote headers
- `quote_items` - Line items
- `quote_versions` - Revision tracking
- `invoices` - Invoice headers
- `invoice_items` - Line items

### **Assets**
- `assets` - All asset types (with fields: name, type, customer_id, serial_number, etc.)
- `asset_types` - Equipment, License, RAMS, Insurance, etc.
- `asset_service_schedules` - Recurring and one-time service schedules per asset
  - Fields: asset_id, service_type, frequency, next_service_date, notification_days, auto_create_job
- `asset_maintenance_history` - Completed maintenance records
  - Fields: asset_id, service_date, performed_by, notes, job_id, next_due_date, total_parts_cost, total_labor_cost
- `asset_assignments` - Engineer/job assignments
- `asset_notifications_sent` - Track which notifications sent (prevent duplicates)
  - Fields: asset_id, notification_type, sent_date, recipients, service_schedule_id

### **Ticketing**
- `tickets` - Support tickets
  - Fields: title, customer_id, asset_id, parent_status, sub_status, priority, source (email/phone/portal), assigned_to, created_at
- `ticket_statuses` - Configurable parent statuses
  - Fields: name, order, customer_visible, color
- `ticket_sub_statuses` - Configurable sub-statuses per parent status
  - Fields: parent_status_id, name, order, staff_only, auto_escalate_priority
- `ticket_messages` - Comments and updates
  - Fields: ticket_id, user_id, message, communication_type (email/phone/note/system), direction (inbound/outbound), visible_to_customer
- `ticket_attachments` - File uploads
- `ticket_relationships` - Parent/child ticket linking
  - Fields: parent_ticket_id, child_ticket_id, relationship_type (blocks/related/duplicate)
- `ticket_priority_history` - Track priority changes
  - Fields: ticket_id, old_priority, new_priority, reason, changed_by, changed_at
- `ticket_status_history` - Track all status changes
  - Fields: ticket_id, old_parent_status, new_parent_status, old_sub_status, new_sub_status, changed_by, changed_at

### **Jobs & Projects**
- `projects` - Main project records
- `jobs` - Individual jobs/tasks
- `job_assignments` - Engineer assignments
- `job_parts_used` - Parts consumed during job
  - Fields: job_id, part_id, quantity, cost_price, sell_price, reason_for_replacement, failed_part_photo, warranty_claim
- `timesheets` - Time tracking
- `milestones` - Project milestones

### **Integration**
- `api_keys` - External API credentials
- `webhooks` - Webhook configurations
- `integration_logs` - Sync history

### **Email Management**
- `email_accounts` - Monitored email accounts (IMAP/POP3 settings)
- `emails` - All incoming/outgoing emails
  - Fields: from, to, cc, bcc, subject, body, html_body, thread_id, ticket_id, job_id, user_id, direction (inbound/outbound)
- `email_attachments` - Email file attachments
- `email_templates` - Reusable email templates
- `email_routing_rules` - Auto-assignment rules based on email content
- `email_threads` - Thread grouping for conversations

### **Warranty Management**
- `warranties` - Warranty records linked to assets
  - Fields: asset_id, start_date, end_date, manufacturer, terms_document, coverage_type, status
- `warranty_claims` - Manufacturer warranty claims
  - Fields: warranty_id, job_id, claim_reference, status, submitted_date, expected_resolution_date, claim_amount, approved_amount, rejection_reason
- `warranty_claim_documents` - Supporting documentation for claims
  - Fields: claim_id, document_type (photo, report, certificate), file_path, uploaded_by
- `warranty_manufacturers` - Manufacturer contact details and claim procedures
  - Fields: name, contact_email, claim_submission_method, average_processing_days

### **Purchase Orders & Enhanced Parts**
- `purchase_orders` - Orders to suppliers (already listed but enhanced)
  - Fields: po_number, supplier_id, status, total, expected_delivery_date, actual_delivery_date, approved_by, approval_date
- `purchase_order_items` - Line items on PO
  - Fields: po_id, part_id, quantity, unit_price, received_quantity
- `goods_received_notes` - GRN records
  - Fields: po_id, received_date, received_by, notes, quality_check_passed
- `stock_reservations` - Parts reserved for specific jobs
  - Fields: part_id, job_id, quantity, reserved_date, released_date
- `stock_transfers` - Movement between locations
  - Fields: part_id, from_location_id, to_location_id, quantity, transfer_date, transferred_by

### **Sub-contractors**
- `subcontractors` - External engineer companies
  - Fields: company_name, contact_name, email, phone, specializations, certifications, insurance_expiry, performance_rating
- `subcontractor_jobs` - Jobs assigned to external engineers
  - Fields: subcontractor_id, job_id, agreed_rate, status, acceptance_date
- `subcontractor_documents` - Paperwork from external engineers
  - Fields: job_id, subcontractor_id, document_type, file_path, uploaded_date, approval_status, approved_by
- `subcontractor_invoices` - Invoices from sub-contractors
  - Fields: subcontractor_id, job_id, invoice_number, amount, status, payment_date
- `subcontractor_ratings` - Performance ratings per job
  - Fields: subcontractor_id, job_id, quality_rating, timeliness_rating, communication_rating, notes

### **Workflow Builder**
- `workflows` - Workflow definitions
  - Fields: name, type, description, is_active, is_default, team_id, version, created_by
- `workflow_statuses` - Parent statuses per workflow
  - Fields: workflow_id, name, display_name, color, icon, customer_visible, order, sla_hours
- `workflow_sub_statuses` - Sub-statuses per parent
  - Fields: workflow_status_id, name, staff_only, auto_escalate_priority, order
- `workflow_transitions` - Allowed status transitions
  - Fields: workflow_id, from_status_id, to_status_id, requires_permission, requires_note
- `workflow_rules` - Auto-assignment/notification rules
  - Fields: workflow_id, trigger_event, conditions (JSON), actions (JSON), priority, is_active
- `workflow_conditions` - If-then-else conditions (Phase 2)
  - Fields: rule_id, field_name, operator, value, logic_type (AND/OR)
- `workflow_actions` - Automated actions (Phase 2)
  - Fields: rule_id, action_type, action_config (JSON), order
- `workflow_variables` - Custom variables (Phase 2)
  - Fields: workflow_id, name, data_type, default_value, formula
- `workflow_versions` - Version history
  - Fields: workflow_id, version_number, changes (JSON), published_at, published_by
- `workflow_analytics` - Performance metrics
  - Fields: workflow_id, status_id, avg_time_in_status, tickets_count, sla_compliance_rate, date
- `workflow_templates` - Pre-built templates
  - Fields: name, category, description, workflow_config (JSON), industry, popularity_score

### **Audit Logging**
- `audit_logs` - Comprehensive activity log
  - Fields: user_id, action, entity_type, entity_id, old_values (JSON), new_values (JSON), ip_address, user_agent, location, reason, context (JSON), created_at
- `audit_log_access` - Track who accessed audit logs
  - Fields: auditor_id, accessed_at, filters_used (JSON), records_viewed, exported
- `security_events` - Failed logins, suspicious activity
  - Fields: event_type, user_id, ip_address, details (JSON), severity, created_at

### **Settings Management**
- `system_settings` - System-wide configuration
  - Fields: key, value (JSON), data_type, category, is_sensitive, updated_by, updated_at
- `team_settings` - Team-specific configuration
  - Fields: team_id, key, value (JSON), updated_by, updated_at
- `settings_versions` - Settings change history
  - Fields: setting_type, key, old_value (JSON), new_value (JSON), changed_by, reason, changed_at
- `feature_flags` - Feature toggle configuration
  - Fields: name, enabled, rollout_percentage, team_ids (JSON), user_ids (JSON), description

### **Backup Management**
- `backups` - Backup records
  - Fields: type (full/incremental/differential), status, started_at, completed_at, file_path, file_size, checksum, storage_location, retention_until
- `backup_logs` - Backup operation logs
  - Fields: backup_id, log_level, message, created_at
- `backup_restorations` - Restoration history
  - Fields: backup_id, restored_by, restoration_type, status, started_at, completed_at, notes

### **Compliance & Policies**
- `policies` - Company policies
  - Fields: name, type, content (TEXT), version, effective_date, review_date, is_published, is_public, requires_acceptance
- `policy_versions` - Policy version history
  - Fields: policy_id, version_number, content (TEXT), published_at, published_by
- `policy_acceptances` - User policy acceptances
  - Fields: policy_id, user_id, policy_version, accepted_at, ip_address
- `cookie_consent` - Cookie consent tracking
  - Fields: user_id, session_id, strictly_necessary, functional, analytics, marketing, consent_version, consented_at, ip_address, user_agent
- `cookie_definitions` - List of cookies used
  - Fields: name, category, provider, purpose, duration, is_active
- `gdpr_requests` - Data subject access requests
  - Fields: user_id, request_type (access/rectify/erase/port/object), status, requested_at, processed_at, processed_by, notes
- `data_exports` - User data export records
  - Fields: user_id, export_type, format, file_path, status, requested_at, completed_at, expires_at
- `data_retention_rules` - Retention policies
  - Fields: data_type, retention_period_days, deletion_method (delete/anonymize), is_active

### **Theming & Customization**
- `team_themes` - White-label branding per team (logo, colors, dark/light mode)
- `user_preferences` - Per-user settings (theme, sidebar collapsed, dashboard layout)
- `dashboard_widgets` - Available widget definitions
- `user_dashboard_widgets` - User's customized dashboard layout
- `saved_filters` - User-saved table filters and searches
- `user_views` - Saved view preferences (table/card/list per page)

### **Custom Fields**
- `custom_fields` - Custom field definitions
  - Fields: entity_type (users/assets/customers/etc), field_name, display_label, field_type, description, default_value, placeholder, is_required, is_visible_to_customers, display_order, validation_rules (JSON), permissions (JSON), conditional_logic (JSON), section_id, created_by, team_id
- `custom_field_sections` - Field grouping sections
  - Fields: entity_type, section_name, description, display_order, is_collapsible, icon, conditional_visibility (JSON), team_id
- `custom_field_values` - Custom field values (polymorphic)
  - Fields: entity_type, entity_id, custom_field_id, value (TEXT), created_at, updated_at
- `custom_field_options` - Dropdown/select options
  - Fields: custom_field_id, option_label, option_value, display_order, is_default
- `custom_field_templates` - Pre-built field templates
  - Fields: template_name, entity_type, category, description, fields_config (JSON), is_public, popularity_score, created_by
- `custom_field_history` - Audit trail for custom field changes
  - Fields: entity_type, entity_id, custom_field_id, old_value, new_value, changed_by, changed_at

### **Modular System**
- `installed_modules` - Track installed modules
  - Fields: module_name, module_slug, version, installed_at, installed_by, is_enabled, configuration (JSON), team_id
- `module_dependencies` - Module dependency relationships
  - Fields: module_id, depends_on_module_id, is_required, minimum_version
- `module_permissions` - Permissions per module
  - Fields: module_id, permission_name, permission_slug, description
- `module_settings` - Module-specific settings
  - Fields: module_id, setting_key, setting_value (JSON), updated_by, updated_at, team_id

### **Maintenance Mode**
- `maintenance_modes` - Maintenance mode history
  - Fields: mode_type (frontend_only/staff_only/emergency/full), started_at, ended_at, started_by, ended_by, reason (TEXT), custom_message (TEXT), scheduled_end_time, whitelisted_roles (JSON), whitelisted_user_ids (JSON), background_jobs_behavior (continue/pause/critical_only), is_active, team_id
- `maintenance_access_logs` - Access attempts during maintenance
  - Fields: maintenance_mode_id, user_id, access_granted (BOOLEAN), attempted_at, ip_address, user_agent, blocked_reason
- `maintenance_schedules` - Scheduled maintenance
  - Fields: mode_type, scheduled_start, scheduled_end, auto_enable, auto_disable, notification_sent, notification_time, custom_message (TEXT), whitelisted_roles (JSON), whitelisted_user_ids (JSON), created_by, team_id

---

## **Key Workflows**

### **Quote to Job Conversion**
1. Create quote from catalog or scratch
2. Add line items with pricing
3. Apply discount → triggers approval if > threshold
4. Manager approves/rejects
5. Send to customer via email (PDF attached)
6. Customer views in portal + e-signs
7. System converts to Job/Project
8. Engineers assigned, schedule created
9. Kanban board updated

### **Asset Expiry Management**
1. System checks expiry dates daily (scheduled job)
2. Sends notifications 30/14/7 days before expiry
3. Dashboard shows expiring items
4. Manager updates license/insurance/RAMS
5. New documents uploaded via Media Library
6. Activity log tracks all changes

### **Ticket Lifecycle**
1. Customer submits ticket (or staff creates)
2. Auto-assign based on rules (or manual)
3. Engineer responds, updates status
4. Internal notes for staff discussion
5. Customer receives email updates
6. Attach related assets/jobs
7. Resolve → customer confirms → close
8. Satisfaction survey (optional)

### **Scheduled Asset Service Notification**
**Scenario:** Customer V has Asset W that needs Service X on Date Y, notify Z days in advance

1. **Daily Scheduled Task** (Laravel schedule, runs at 6:00 AM)
   - Query all assets with upcoming service dates
   - Filter by notification thresholds (e.g., 30/14/7 days before, or custom per asset)

2. **For each asset needing notification:**
   - Check if notification already sent for this service date
   - Calculate days until service due
   - If matches notification threshold (Z days before Date Y):
     - Generate notification

3. **Multi-recipient Email Notification:**
   - **To Customer V (asset owner):**
     - Subject: "Service Due: [Asset W] - [Service X] on [Date Y]"
     - Details: Asset name, service type, due date, description
     - CTA: "Schedule Service" (links to customer portal)
   - **To Assigned Engineer** (if pre-assigned):
     - Same details + job preparation info
   - **CC Internal Team:**
     - Service coordinator/manager
     - Shows in dashboard "Upcoming Services" widget

4. **Database Updates:**
   - Log notification in `notifications` table
   - Update asset record: `last_service_reminder_sent`
   - Create activity log entry (Spatie Activity Log)
   - Optionally: Auto-create draft job/ticket for the service

5. **Dashboard Indicators:**
   - **Staff Dashboard:** "5 services due this week" widget
   - **Customer Dashboard:** "Your asset [W] needs service" card
   - **Calendar View:** Service due dates highlighted

6. **Escalation (if overdue):**
   - If service date passes without completion:
     - Mark asset as "Overdue Service" (red status)
     - Send escalation email to manager
     - Show in compliance reports
     - Daily reminders until completed

7. **Completion Workflow:**
   - Engineer completes service (via job/ticket)
   - System calculates next service date (if recurring)
   - Updates asset maintenance history
   - Sends confirmation email to customer
   - Resets notification cycle for next occurrence

**Configuration Options:**
- Notification thresholds per asset type (e.g., critical assets: 30/14/7 days, standard: 14/7 days)
- Email template customization per team (white-label)
- Option to disable auto-notifications per asset
- Notification channels: Email, SMS (optional), Push (PWA), In-app

### **Email-to-Job Complete Workflow**
**Example: Customer emails about LGMG AR14J breakdown with error codes 12, 13, 14 in London**

#### **Step 1: Email Receipt & Parsing (Automated)**

1. **System monitors** support@yourcompany.com via IMAP
2. **New email arrives:**
   ```
   From: david@customer.com
   Subject: URGENT - LGMG AR14J Down - Error Codes 12, 13, 14

   Hi,
   Our LGMG AR14J scissor lift at our London site is showing error
   codes 12, 13, 14 and won't operate. We need urgent assistance.

   Contact: David Smith
   Location: 123 Main St, London
   ```

3. **Email Parser extracts:**
   - Machine model: "LGMG AR14J"
   - Error codes: 12, 13, 14
   - Location: "London" + address
   - Contact: "David Smith"
   - Priority: "URGENT" keyword detected → High priority
   - Customer: Matches david@customer.com to existing customer record

#### **Step 2: Ticket Auto-Creation**

1. **Create ticket automatically:**
   - Title: "LGMG AR14J - Error Codes 12, 13, 14"
   - Customer: Matched from email
   - Contact: David Smith
   - Priority: High (from "URGENT" keyword)
   - Status: New
   - Source: Email

2. **Asset Linking:**
   - Search customer's assets for "LGMG AR14J"
   - If found: Link ticket to asset (shows full maintenance history)
   - If not found: Create asset placeholder for assignment later

3. **Warranty Check (Automatic):**
   - Check if asset has active warranty
   - Display warranty badge on ticket
   - Pre-populate "Warranty" or "Chargeable" status

#### **Step 3: Smart Assignment & Routing**

1. **Auto-assignment rules trigger:**
   - Asset type "Scissor Lift" → Route to hydraulics specialist team
   - Location "London" → Assign to London-based engineer
   - Priority "High" → Notify manager immediately

2. **Engineer assigned:** John (London specialist)

3. **Notifications sent:**
   - Email to John: "New urgent ticket assigned"
   - SMS to John (if enabled): "LGMG AR14J down in London"
   - Manager dashboard: Alert for urgent ticket

#### **Step 4: Warranty vs Chargeable Determination**

**Manager reviews ticket:**
- **Warranty status:** Active until Dec 2025 ✅
- **Error codes 12, 13, 14:** Check manufacturer database
  - Error 12: Hydraulic pressure sensor (covered under warranty)
  - Error 13: Electrical fault (covered)
  - Error 14: Software error (covered)
- **Decision:** Warranty claim
- **Status updated:** "Warranty Claim - To be submitted"

**If Chargeable:**
- Create quote for diagnostic + repair
- Send quote to customer via email
- Wait for approval before proceeding

#### **Step 5: Parts Availability Check**

1. **Check common parts for these error codes:**
   - Hydraulic pressure sensor (Part #HP-1234)
   - Electrical relay (Part #ER-5678)

2. **Stock status:**
   - HP-1234: **In stock** (5 units in warehouse)
   - ER-5678: **On order** (Expected: 2 days, PO #12345)

3. **Job status updated:**
   - "Waiting for parts - ER-5678 due in 2 days"
   - Customer notification sent automatically

#### **Step 6: Engineer Dispatch**

**Once parts available:**
1. **Create job from ticket**
2. **Assign engineer:** John
3. **Schedule:** Tomorrow 9:00 AM
4. **Parts allocated:**
   - Reserve HP-1234 (1 unit) from warehouse
   - Reserve ER-5678 (1 unit) when received

5. **Job pack generated:**
   - Asset history (previous services, parts used)
   - Error code reference documentation
   - Manufacturer warranty claim requirements
   - Customer contact: David Smith
   - Site address: 123 Main St, London

#### **Step 7: On-Site Service (Engineer Mobile App)**

**John arrives on site:**
1. **Clock in** via GPS (confirms London location)
2. **Update status:** "In Progress"
3. **Diagnoses issue:**
   - Error 12: Faulty pressure sensor confirmed
   - Error 13: Relay malfunction
   - Error 14: Software needs reset

4. **Parts used:**
   - HP-1234: 1 unit (£45 cost, £0 charge - warranty)
   - ER-5678: 1 unit (£30 cost, £0 charge - warranty)

5. **Photos uploaded:**
   - Failed pressure sensor (for warranty claim)
   - Error code screen
   - Repaired unit operational

6. **Job notes:** "Replaced faulty sensor and relay. Reset software. Unit tested and operational."

7. **Customer signature:** David signs on mobile device

8. **Clock out:** 11:30 AM (2.5 hours labor)

#### **Step 8: Warranty Claim Submission**

**Office processes warranty claim:**
1. **Create warranty claim record:**
   - Claim reference: WC-2025-0234
   - Manufacturer: LGMG
   - Parts claimed: HP-1234, ER-5678
   - Labor claimed: 2.5 hours @ £50/hr = £125
   - Total claim: £200 (parts £75 + labor £125)

2. **Required documentation (auto-collected):**
   - ✅ Photos of failed parts
   - ✅ Error code logs
   - ✅ Service report (auto-generated)
   - ✅ Customer signature
   - ✅ Warranty certificate (linked to asset)

3. **Submit to LGMG:**
   - Email with attachments
   - Track claim status: "Submitted - Awaiting review"
   - Expected resolution: 14 days

#### **Step 9: Customer Communication**

**Automatic updates sent to David:**
1. ✅ "Ticket created - Engineer assigned"
2. ✅ "Parts on order - Expected completion in 2 days"
3. ✅ "Job scheduled for tomorrow 9 AM"
4. ✅ "Engineer en route"
5. ✅ "Job completed - Asset operational"
6. ✅ "Warranty claim submitted - No charge to you"

**Customer portal shows:**
- Complete communication history
- Job status timeline
- Parts used
- Warranty claim status
- Service report PDF (downloadable)

#### **Step 10: Claim Tracking & Resolution**

**Warranty claim lifecycle:**
1. **Day 1:** Submitted to LGMG
2. **Day 3:** LGMG emails: "Claim received, under review"
   - System automatically updates status: "Under Review"
3. **Day 10:** LGMG emails: "Claim approved - £200"
   - Status: "Approved - Payment expected 30 days"
4. **Day 40:** Payment received
   - Status: "Paid"
   - Job marked as complete

**If claim rejected:**
- Email alert to manager
- Rejection reason logged
- Option to appeal or convert to chargeable
- Create invoice for customer if appropriate

#### **Step 11: Invoice & Paperwork**

**For chargeable jobs:**
1. **Invoice auto-generated:**
   - Parts: £75
   - Labor: 2.5hrs @ £50 = £125
   - Travel: £20
   - Total: £220

2. **Send to customer:**
   - PDF invoice via email
   - Payment link (if online payment enabled)
   - Payment terms: Net 30

3. **Track payment:**
   - Status: Pending → Paid
   - Payment reminder emails (7 days before due, on due date, overdue)

**For warranty jobs:**
- Certificate of completion sent to customer
- No charge invoice (£0.00) for records
- Warranty claim reference provided

#### **Step 12: Complete Communication Thread**

**All emails tracked in ticket:**
- ✅ Original email from David (inbound)
- ✅ Auto-reply: "Ticket created" (outbound)
- ✅ "Engineer assigned" notification (outbound)
- ✅ "Parts on order" update (outbound)
- ✅ "Job scheduled" confirmation (outbound)
- ✅ "Job completed" summary (outbound)
- ✅ David's reply: "Thank you!" (inbound)
- ✅ Service report PDF (outbound)

**Searchable & exportable:**
- Search all communications for this customer
- Export email thread as PDF
- Complete audit trail

---

### **Advanced Workflow: Parent/Sub-Status System with Internal Tickets**
**Granular tracking of complex multi-step processes**

#### **Parent Status & Sub-Status Architecture**

**Parent Status:** High-level workflow stage (visible to customer)
**Sub-Status:** Detailed internal tracking (visible to staff only)

```
Parent Status: "Pending Manufacturer Auth"
└─ Sub-Status: "Sent to manufacturer"
└─ Sub-Status: "Reply received"
└─ Sub-Status: "Additional info required"
```

#### **Complete Warranty Breakdown Process Flow**

---

##### **Step 1: New Communication Received**

**Communication Source:** Email, phone call, web form, customer portal

**Auto-Actions:**
- Create parent ticket
- Extract job details from communication
- Link to customer and asset (if identifiable)

**Status:**
- **Parent Status:** `New`
- **Sub-Status:** `Auto-created from email`
- **Priority:** Normal (auto-escalates based on keywords)

**System Actions:**
- Send auto-acknowledgment email
- Assign to support queue
- Log communication in ticket thread

---

##### **Step 2: Initial Response - Request Missing Info**

**Staff Action:** Review ticket, create job, request serial number/address

**Communication Sent:** Email template "Additional Information Required"

**Status Update:**
- **Parent Status:** `Job Created`
- **Sub-Status:** `Additional info required`
- **Job Flag:** Created (linked to ticket)

**Visual Indicator:**
- 🟡 Yellow badge "Awaiting Customer Response"
- Countdown timer: "Response due in 24 hours"

**Auto-Escalation Rule:**
- If no reply in 48 hours → Send reminder email
- If no reply in 96 hours → Escalate to manager

---

##### **Step 3: Customer Reply Received**

**Email Recognition:** System detects reply via embedded reference (ticket #12345)

**Auto-Actions:**
- Parse email for requested information
- Update ticket thread
- **Priority Boost:** Ticket moved to priority queue

**Status Update:**
- **Parent Status:** `Job Created`
- **Sub-Status:** `Reply received` ✅
- **Priority:** High (auto-escalated)

**Staff Notification:**
- Desktop notification: "Customer replied to Ticket #12345"
- Email digest: "3 customer replies awaiting action"

---

##### **Step 4: Send to Manufacturer for Auth**

**Staff Action:**
- Update job with customer-provided details
- Prepare manufacturer auth request
- Submit via manufacturer portal or email

**Status Update:**
- **Parent Status:** `Pending Manufacturer Auth`
- **Sub-Status:** `Sent to manufacturer`
- **Manufacturer Claim Record:** Created (linked to ticket)
- **Claim Ref:** Auto-generated or manual entry

**Customer Notification:**
- Email: "We've submitted your warranty claim to [Manufacturer]"
- Portal update: "Status: Awaiting manufacturer authorization"

**SLA Tracking:**
- Expected response: 5 business days (from manufacturer database)
- Countdown timer: "Manufacturer response due in 5 days"
- Auto-reminder: Email manufacturer if no reply in 7 days

---

##### **Step 5: Manufacturer Replies - Additional Info Required**

**Email Recognition:** Reply from manufacturer email address

**Auto-Actions:**
- Update ticket thread
- **Priority Boost:** High priority (manufacturer waiting)
- Parse email for requested items

**Status Update:**
- **Parent Status:** `Pending Manufacturer Auth`
- **Sub-Status:** `Reply received - additional info required` ⚠️
- **Priority:** High

**Staff Alert:**
- Toast notification: "Manufacturer replied - action required"
- Email to assigned engineer: "Photos needed for claim #ABC123"

---

##### **Step 6: Request Photos from Customer**

**Staff Action:** Send email request to customer for photos

**Status Update:**
- **Parent Status:** `Pending Manufacturer Auth`
- **Sub-Status:** `Awaiting customer photos`

**Customer Email:**
- Template: "Please provide photos of [failed part]"
- Photo upload link (direct to ticket)
- Mobile-friendly upload interface

**Auto-Escalation:**
- If no photos in 24 hours → Send reminder
- If no photos in 72 hours → Call customer (create task)

---

##### **Step 7: Photos Received - Forward to Manufacturer**

**Customer Action:** Uploads photos via link or email reply

**Auto-Actions:**
- Attach photos to ticket
- **Priority Boost:** High (awaiting action)

**Staff Action:** Forward photos to manufacturer

**Status Update:**
- **Parent Status:** `Pending Manufacturer Auth`
- **Sub-Status:** `Sent to manufacturer` ✅
- **Photos Attached:** 3 files

**Manufacturer Email:**
- Auto-attach photos to email template
- Include claim reference
- CC support team for tracking

---

##### **Step 8: Manufacturer Authorizes - Auth Ref Given**

**Email Recognition:** Manufacturer reply with authorization

**Auto-Actions:**
- Parse email for auth reference (e.g., "ABC/123")
- **Priority Boost:** High (ready to proceed)
- Extract claim amount if provided

**Status Update:**
- **Parent Status:** `Pending Schedule`
- **Sub-Status:** `Manufacturer auth received` ✅
- **Job Flag:** `Warranty - Auth Ref: ABC/123`
- **Claim Ref:** ABC/123 (saved to warranty_claims table)

**Staff & Customer Notification:**
- Staff: "Claim authorized - check stock and schedule"
- Customer: "Great news! Your warranty claim is approved"

---

##### **Step 9: Stock Check & Order Parts**

**Staff Action:** Check parts availability

**Stock Status:**
- Part A: In stock ✅
- Part B: **Out of stock** → Needs purchase requisition

**Auto-Actions:**
- **Create Internal Ticket:** "Purchase Requisition Required"
- Auto-assign based on value:
  - < £500: Auto-approve
  - £500-£2,000: Line manager approval
  - > £2,000: Senior manager approval + quote comparison

**Status Update:**
- **Parent Status:** `Pending Schedule`
- **Sub-Status:** `Pending requisition approval`

---

##### **Step 9a: Internal Ticket - Purchase Requisition**

**Internal Ticket Auto-Created:**

```
Ticket Type: Internal - Purchase Requisition
Parent Ticket: #12345 (Warranty breakdown)
Requested By: Joe (Support)
Assigned To: Line Manager (Sarah)
Priority: High (parent ticket waiting)
Value: £350

Items Requested:
- Part B (Qty: 2) - £175 each

Reason: Warranty claim ABC/123 - customer waiting
Expected Delivery: 3-5 days
Supplier: ACME Parts Ltd
```

**Line Manager Action:**
- Review request in dashboard widget "Pending Approvals"
- Click "Approve" or "Reject" with notes
- If rejected: Request alternative supplier/part

**Status Update:**
- **Internal Ticket Status:** `Pending Approval`
- **Parent Ticket Sub-Status:** `Pending requisition approval`

**Line Manager Approves:**

**Auto-Actions:**
- Close internal ticket (status: `Approved`)
- Create Purchase Order automatically
- Send PO to supplier via email
- Update parent ticket

**Internal Ticket Status:**
- **Status:** `Closed - Approved`
- **Approved By:** Sarah (Line Manager)
- **Approved Date:** 24 Dec 2025
- **PO Created:** PO #12345

**Visibility:**
- Internal ticket remains linked to parent ticket
- Viewable from parent ticket "Related Tickets" section
- Audit trail of approval process

---

##### **Step 10: Parts Placed on Order**

**Auto-Actions from PO Creation:**

**Status Update:**
- **Parent Status:** `Pending Schedule`
- **Sub-Status:** `Parts on order - PO #12345 - Due 1 Jan 2026`
- **Expected Delivery:** 1 Jan 2026

**Customer Notification:**
- Email: "Parts ordered - expected arrival 1 Jan 2026"
- Portal: Shows delivery countdown "Parts arriving in 8 days"

**Staff Dashboard:**
- Widget: "Jobs Waiting for Parts" shows ticket in list
- Daily digest: "5 jobs have parts arriving this week"

---

##### **Step 11: Shipment Arrives - Parts Checked**

**Warehouse Action:** Receive parts, create Goods Received Note (GRN)

**Auto-Actions:**
- Update PO status: `Received`
- Update stock levels
- **Priority Boost:** High (parts available - schedule now)
- **Auto-Assign:** Ticket assigned to scheduler/coordinator

**Status Update:**
- **Parent Status:** `Pending Schedule`
- **Sub-Status:** `Parts received - ready to schedule` ✅

**Customer Notification:**
- Email: "Good news! Parts arrived - scheduling your repair"

**Scheduler Alert:**
- Desktop notification: "Ticket #12345 ready to schedule"
- Appears in "Priority Schedule Queue"

---

##### **Step 12: Job Scheduled with External Engineer**

**Scheduler Action:**
- Assign job to Joe Bloggs (External sub-contractor)
- Schedule date: 25 Dec 2025
- Allocate parts to engineer

**Status Update:**
- **Parent Status:** `Scheduled`
- **Schedule Date:** 25 Dec 2025, 9:00 AM
- **Engineer:** Joe Bloggs (External)
- **Sub-Status:** `Parts sent to engineer`

**Notifications Sent:**
- **Customer:** "Your repair is scheduled for 25 Dec 2025 with engineer Joe Bloggs"
- **External Engineer:** Email + SMS with job details, access to portal
- **Staff:** Added to calendar and Gantt chart

**Sub-contractor Portal Shows:**
- Job details
- Customer contact
- Parts allocated
- Required documentation checklist
- Upload area for reports

---

##### **Step 13: Job Attended - Reports Submitted**

**External Engineer Action (via portal):**
- Upload completed job sheet
- Upload photos of repair
- Log parts used
- Customer signature captured
- Submit for review

**Auto-Actions:**
- **Priority Boost:** High (needs QC review)
- Auto-assign to QC team

**Status Update:**
- **Parent Status:** `Repair Complete`
- **Sub-Status:** `Pending QC review` 🔍
- **Priority:** High

**QC Team Alert:**
- Notification: "New job report needs review"
- Dashboard widget: "Pending QC Reviews (3)"

**QC Review Checklist:**
- ✅ All required photos uploaded?
- ✅ Customer signature present?
- ✅ Parts used match authorization?
- ✅ Job notes clear and complete?
- ✅ Compliance certificates attached? (if required)

**QC Actions:**
- **Approve:** Move to next step
- **Reject:** Request re-submission from engineer (creates internal ticket)

---

##### **Step 14: Sub-contractor Invoice Required**

**QC Approved - Next Step Auto-Triggered:**

**Status Update:**
- **Parent Status:** `Repair Complete`
- **Sub-Status:** `Pending sub-contractor invoice`

**Auto-Actions:**
- Email to external engineer: "Please submit invoice for job #12345"
- Portal reminder: "Invoice required for completed job"

**Auto-Escalation:**
- If no invoice in 7 days → Reminder email
- If no invoice in 14 days → Alert to accounts team

---

##### **Step 15: Invoice Received - Review Required**

**External Engineer Submits Invoice (via portal or email):**

**Auto-Actions:**
- Parse invoice (PDF OCR or structured data)
- Match to job and agreed rate
- **Priority Boost:** High (needs approval)
- Auto-assign to accounts team

**Status Update:**
- **Parent Status:** `Repair Complete`
- **Sub-Status:** `Invoice under review` 💰
- **Priority:** High

**Invoice Review Dashboard:**
- Shows invoice details vs agreed rate
- Highlights discrepancies (if any)
- One-click approve/reject

**Approval Actions:**
- **Approve:** Schedule payment, update status
- **Query:** Send questions to engineer (email tracked in ticket)
- **Reject:** Request corrected invoice

**Approved Invoice:**
- Status updates to: `Invoice approved - payment scheduled`
- Added to next payment run
- Engineer notified: "Invoice approved - payment in 14 days"

---

##### **Step 16: Claim Prepared for Submission**

**All Prerequisites Met:**
- ✅ Job completed and QC approved
- ✅ Sub-contractor invoice received and approved
- ✅ All documentation uploaded
- ✅ Parts costs recorded
- ✅ Labour hours logged

**Auto-Actions:**
- Calculate total claim:
  - Parts: £456
  - Labour: £123 (2.5 hours @ £49.20/hr)
  - Total: £579
- Generate claim submission document
- Attach all supporting documents

**Status Update:**
- **Parent Status:** `Repair Complete`
- **Sub-Status:** `Ready for final submission` ✅

**Staff Alert:**
- "Claim #ABC/123 ready for submission to manufacturer"
- One-click submit button

---

##### **Step 17: Claim Submitted to Manufacturer**

**Staff Action:** Submit claim via manufacturer portal or email

**Status Update:**
- **Parent Status:** `Repair Complete`
- **Sub-Status:** `Submitted to manufacturer`
- **Submission Date:** Logged
- **Expected Response:** 14 days (from manufacturer SLA)

**Auto-Actions:**
- Email to manufacturer with all attachments
- Copy support team
- Start SLA countdown timer

**Customer Notification:**
- "Your warranty claim has been submitted - we'll update you on approval"

**Auto-Escalation:**
- If no reply in 14 days → Send follow-up email
- If no reply in 21 days → Escalate to manager
- If no reply in 30 days → Phone call logged

---

##### **Step 18: Manufacturer Authorization Given - CLOSED**

**Manufacturer Email:** "Claim approved - Ref 1234 - £579"

**Auto-Actions:**
- Parse email for claim amount
- Compare to submitted amount (flag discrepancies)
- Update claim record

**Final Status:**
- **Parent Status:** `Closed - Warranty Approved` ✅
- **Sub-Status:** `Payment pending from manufacturer`
- **Claim Details:**
  - Labour: £123 ✅
  - Parts: £456 ✅
  - Total Approved: £579 ✅
  - Manufacturer Ref: 1234
  - Expected Payment: Net 30 days

**Notifications:**
- **Customer:** "Your repair is complete - no charge to you!"
- **Accounts Team:** "Warranty payment expected: £579 in 30 days"
- **Manager:** Daily digest shows completed warranty claims

**Ticket Archive:**
- Complete communication history preserved
- All related tickets linked (internal requisitions, invoices)
- Searchable and reportable
- Used for future similar claims (AI learning)

---

#### **Visual Status Board (Kanban-Style)**

```
┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│ New         │ │ Job Created │ │ Pending Auth│ │ Scheduled   │
│ (2)         │ │ (5)         │ │ (8)         │ │ (12)        │
├─────────────┤ ├─────────────┤ ├─────────────┤ ├─────────────┤
│ #12345 🔴   │ │ #12340 🟡   │ │ #12338 🟡   │ │ #12330 🟢   │
│ Auto-created│ │ Info req'd  │ │ Sent to MFR │ │ 25 Dec @9AM │
│ 5 mins ago  │ │ Awaiting    │ │ Due: 5 days │ │ Joe (Ext)   │
│             │ │ customer    │ │             │ │             │
│ #12346 🟡   │ │             │ │ #12337 🔴   │ │             │
│ Phone call  │ │ #12339 🟡   │ │ Photos req'd│ │             │
│             │ │ Reply recv  │ │ URGENT      │ │             │
└─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘

┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│ Repair Done │ │ Invoice Rev │ │ Closed      │
│ (6)         │ │ (4)         │ │ (145)       │
├─────────────┤ ├─────────────┤ ├─────────────┤
│ #12328 🔴   │ │ #12320 🔴   │ │ Filter by:  │
│ QC Review   │ │ Pending Appr│ │ • Today (8) │
│ Priority    │ │ £450        │ │ • Week (32) │
│             │ │             │ │ • Month(145)│
│ #12326 🟡   │ │ #12318 🟡   │ │             │
│ Need invoice│ │ Query sent  │ │ 💰 £45,600  │
│ Joe Ext     │ │             │ │ recovered   │
└─────────────┘ └─────────────┘ └─────────────┘
```

**Legend:**
- 🔴 High Priority (action required)
- 🟡 Normal Priority (on track)
- 🟢 Low Priority / Scheduled

---

#### **Priority Escalation Rules**

**Auto-Escalate to High Priority When:**
- Customer reply received (needs review)
- Manufacturer reply received (needs action)
- Parts arrived (ready to schedule)
- Job completed (needs QC review)
- Invoice received (needs approval)

**De-Escalate Priority When:**
- Waiting for external response (customer, manufacturer, engineer)
- Scheduled (date set, no action needed)

**Escalation Triggers:**
- No response from customer in 48 hours → Send reminder
- No response from manufacturer in 14 days → Follow up
- Invoice pending review > 3 days → Alert manager
- QC review pending > 24 hours → Alert coordinator

---

#### **Related/Internal Tickets System**

**Parent Ticket:** Main customer-facing ticket
**Child Tickets:** Internal tasks that block parent progress

**Types of Internal Tickets:**
1. **Purchase Requisition Approval**
   - Auto-created when parts needed
   - Assigned based on value threshold
   - Blocks parent until approved

2. **QC Review Required**
   - Auto-created when external engineer submits report
   - Assigned to QC team
   - Blocks final invoicing

3. **Document Request**
   - Created when missing paperwork
   - Assigned to engineer or customer
   - Blocks claim submission

4. **Manager Escalation**
   - Created when SLA breached
   - High value claim approval
   - Customer complaint

**Internal Ticket Visibility:**
- Not visible to customers
- Shown in "Related Tickets" tab on parent
- Counted in parent ticket: "Blocked by 2 internal tickets"
- Dashboard shows: "You have 5 pending approvals"

---

#### **Communication Threading**

**All Communication Types Tracked:**
- 📧 Email (inbound/outbound)
- 📞 Phone call (logged manually)
- 💬 Live chat (if implemented)
- 📝 Internal notes (staff only)
- 🔔 System notifications
- 📄 Document uploads

**Thread Display:**
```
Ticket #12345 - LGMG AR14J Breakdown
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📧 24 Dec 2025 09:15 - David (Customer) → Support
   "URGENT - Our scissor lift is down with error codes..."

📧 24 Dec 2025 09:17 - System → David
   "Thank you for contacting us. Ticket #12345 created."

📝 24 Dec 2025 09:20 - Joe (Staff) - Internal Note
   "Linked to asset LGMG-001. Warranty active until Dec 2026."

📧 24 Dec 2025 09:25 - Joe (Staff) → David
   "We need the serial number. Please provide..."

📧 24 Dec 2025 10:30 - David → Support
   "Serial number is XYZ123..."

📧 24 Dec 2025 11:00 - System → LGMG Manufacturer
   "Warranty claim request - Auth needed..."

📧 26 Dec 2025 14:30 - LGMG → Support
   "Please provide photos of failed part..."

📞 26 Dec 2025 15:00 - Joe called David
   "Requested photos via email link. David will send today."

📄 26 Dec 2025 16:45 - David uploaded 3 photos

📧 27 Dec 2025 09:00 - System → LGMG
   "Photos attached as requested..."

📧 30 Dec 2025 11:20 - LGMG → Support
   "Claim approved. Auth ref ABC/123. Amount: £579"

🔔 30 Dec 2025 11:21 - System Notification
   "Claim approved - ready to schedule"
```

**Communication Search:**
- Search across all threads
- Filter by type (email, phone, notes)
- Filter by participant
- Export as PDF timeline

---



### **Parts Usage Tracking & Trend Analysis**
**Capturing parts used during service and identifying patterns**

#### **Parts Usage During Job/Service:**

1. **Engineer Completes Service** (via mobile PWA or desktop):
   - Select parts used from inventory
   - Enter quantity used per part
   - Option to add parts not in system (ad-hoc)
   - Take photo of failed/replaced part
   - Notes on why part was replaced

2. **System Records:**
   - Links parts to: Job → Asset → Customer
   - Captures: part_id, quantity, cost, reason_for_replacement
   - Updates inventory (reduces stock level)
   - Triggers low stock alert if below reorder point
   - Logs in asset maintenance history

3. **Automatic Calculations:**
   - Job total parts cost
   - Customer lifetime parts cost
   - Asset total maintenance cost
   - Engineer parts usage (for van stock management)

#### **Trend Analysis & Insights:**

**Asset-Level Trends:**
- "This boiler has replaced the heating element 3 times in 2 years" (recurring failure)
- Total parts cost per asset over lifetime
- Comparison: Repair costs vs replacement cost (replace vs repair decision)
- Parts failure timeline (visual chart)

**Part-Level Trends:**
- "Heating Element XYZ fails 60% more than Brand ABC"
- Most frequently replaced parts across all assets
- Average lifespan per part type
- Supplier quality comparison (failure rates by supplier)
- Seasonal trends (e.g., more heating parts in winter)

**Customer-Level Trends:**
- Total parts spend per customer
- Assets with highest maintenance costs
- Customer equipment upgrade recommendations

**Engineer-Level Trends:**
- Parts usage per engineer (detect training needs or theft)
- Van stock optimization (which parts to keep in van)
- Most common parts per service route/region

**Predictive Analytics:**
- "Assets with Part X typically fail within 18 months" → Proactive replacement
- "Customer Y's assets are costing more in parts than new equipment" → Upgrade quote
- Seasonal stock planning based on historical trends
- Warranty claim patterns

#### **Dashboard Widgets for Trends:**

**For Managers:**
- Top 10 failing parts (bar chart)
- Parts cost trend over time (line chart)
- Assets approaching replacement threshold
- Inventory turnover rate
- Supplier performance comparison

**For Engineers:**
- My most-used parts (helps with van stock)
- Quick-add frequently used parts during job entry

**For Customers:**
- Your maintenance cost trends (transparency)
- Parts warranty status
- Recommended upgrades based on repair history

#### **Reports Generated:**

1. **Parts Failure Analysis Report**
   - Which parts fail most frequently
   - Average lifespan per part
   - Cost impact of failures
   - Recommendations for supplier changes

2. **Asset Maintenance Cost Report**
   - Total cost per asset (parts + labor)
   - Repair vs replace recommendations
   - Warranty claim opportunities

3. **Inventory Optimization Report**
   - Slow-moving parts (consider removing from stock)
   - Fast-moving parts (increase stock levels)
   - Van stock recommendations per engineer

4. **Customer Profitability Report**
   - Parts revenue vs cost per customer
   - High-maintenance customers
   - Upsell opportunities (upgrades vs constant repairs)

5. **Predictive Maintenance Report**
   - Assets likely to need service soon (based on historical patterns)
   - Proactive part replacement recommendations
   - Seasonal preparation (stock up before busy season)

#### **Automated Actions Based on Trends:**

- **Low Stock Alert:** "Part X used 15 times this month, only 5 left in stock"
- **Recurring Failure Alert:** "Asset W has failed 3 times with same part - consider upgrade"
- **Supplier Quality Alert:** "Supplier Z parts failing 40% more than average"
- **Customer Notification:** "Your equipment maintenance costs suggest replacement may be cost-effective"
- **Engineer Alert:** "Your van stock for Part Y is depleted"

#### **Integration Benefits:**

- **Quote Generation:** Auto-suggest parts based on asset history
- **Job Planning:** Pre-allocate likely parts needed based on service type + asset
- **Purchasing:** Auto-generate purchase orders when stock hits reorder point
- **Accounting Integration:** Export parts cost data for financial reports
- **Warranty Claims:** Track parts under warranty, auto-flag for claims

#### **Machine Learning Opportunities (Future):**

- Predict part failures before they happen
- Optimize inventory levels based on seasonality + trends
- Suggest preventive maintenance schedules based on usage patterns
- Identify anomalies (unusual part failures = quality issue or sabotage)

---

## **Modern UI/UX Design Guidelines**

### **Design Philosophy**
**Professional SaaS Aesthetic** - Clean, polished business interface with focus on usability and data visualization
- Generous whitespace and clear visual hierarchy
- Data-driven design with emphasis on charts, metrics, and KPIs
- Subtle, professional animations that enhance UX without distraction
- Consistent design patterns across all modules

### **Design System & Components**

#### **Color System**
- **Full White-Label Theming** - Teams can customize their workspace branding
  - Custom team logo upload
  - Primary brand color picker (affects buttons, links, accents)
  - Secondary color for hover states and highlights
  - Success/Warning/Danger colors (customizable or default)
  - Light/Dark mode toggle per user preference

#### **Typography**
- **Sans-serif system font stack** for performance and clarity
  - Headings: Font weight 600-700, clear size hierarchy
  - Body text: Font weight 400, 16px base size for readability
  - Code/monospace: For technical data, API keys, etc.

#### **Icon Library**
- **Heroicons** (Tailwind's official icon set)
  - Outline style for primary navigation and buttons
  - Solid style for active states and emphasis
  - Consistent 20px/24px sizing throughout
  - Proper semantic meaning (no confusing icons)

#### **Component Library**
- **WireUI** - Pre-built Livewire components
  - Buttons (primary, secondary, success, danger, ghost)
  - Form inputs with validation states
  - Modals and slide-overs
  - Dropdowns and select menus
  - Date/time pickers
  - Toast notifications

- **Filament Components** (for admin areas)
  - Rich data tables with filters
  - Form builder
  - Dashboard widgets
  - Stats overview cards

### **Layout & Navigation**

#### **Primary Navigation - Sidebar**
- **Collapsible left sidebar** (280px expanded, 64px collapsed)
  - Logo/team branding at top
  - Main navigation items with icons
  - Grouped sections (e.g., "Sales", "Operations", "Settings")
  - Active state highlighting
  - User avatar and quick settings at bottom
  - Remembers collapsed/expanded state per user

#### **Top Bar**
- Team/tenant selector (if user belongs to multiple teams)
- Global search (with icon, opens search modal)
- Notifications bell with unread count
- Quick actions menu (create quote, new ticket, etc.)
- User profile dropdown (settings, logout, theme toggle)
- Dark/light mode toggle

#### **Content Area**
- Maximum width container (1400px) for readability
- Page header with breadcrumbs
- Page title + primary action button (top right)
- Content cards with subtle shadows and rounded corners

### **Dashboard Design**

#### **Customizable Dashboard System**
**Approach: Preset Layouts + Drag-Drop Customization**

1. **Preset Layouts by Role**
   - **Admin Dashboard:** Team overview, system health, revenue metrics, recent activity
   - **Manager Dashboard:** Team performance, pending approvals, project status, resource allocation
   - **Engineer Dashboard:** My jobs, today's schedule, timesheet quick entry, pending tickets
   - **Customer Dashboard:** My tickets, active projects, recent invoices, asset status

2. **Drag-Drop Widget Customization**
   - Users can rearrange widgets on their dashboard
   - Add/remove widgets from a widget library
   - Resize widgets (small, medium, large, full-width)
   - Save custom layouts per user
   - Reset to role default option

3. **Available Widgets**
   - **Stats Cards:** Revenue, active jobs, open tickets, pending quotes (with trend indicators)
   - **Charts:** Revenue over time, job completion rates, ticket resolution times
   - **Recent Activity:** Timeline of recent actions/changes
   - **Quick Lists:** Upcoming deadlines, expiring assets, pending approvals
   - **Calendar:** Scheduled jobs, engineer availability
   - **Task List:** Personal to-dos and assigned items
   - **Notifications Feed:** Recent alerts and updates

#### **Dashboard Visual Style**
- Card-based layout with grid system (12 columns)
- Subtle drop shadows for depth
- Hover effects on interactive cards
- Loading skeleton states for async data
- Empty states with helpful CTAs
- Responsive grid (stacks on mobile)

### **Data Display Patterns**

#### **Tables & Lists - Flexible Views**
Users can toggle between different view modes depending on data type:

1. **Table View** (Default for most data)
   - Modern Filament-style tables
   - Column sorting (click headers)
   - Multi-column filtering (slide-in filter panel)
   - Bulk selection with checkbox column
   - Bulk actions toolbar (appears when items selected)
   - Pagination with per-page selector (10/25/50/100)
   - Column visibility toggle
   - Export to Excel/CSV button
   - Responsive: horizontal scroll on mobile with sticky first column

2. **Card Grid View** (For products, assets, engineers)
   - Responsive grid (4 cols desktop, 2 cols tablet, 1 col mobile)
   - Card shows key info + thumbnail/icon
   - Quick actions on hover
   - Status badges
   - Better for visual browsing

3. **List View** (For tickets, activity feeds)
   - Compact list with avatars/icons
   - Two-line layout (title + metadata)
   - Status indicators on left edge
   - Quick preview on click
   - Infinite scroll option for long lists

4. **Kanban View** (For jobs, projects, tickets)
   - Horizontal columns by status
   - Drag-drop between columns
   - Card count per column
   - Add new card button at column top
   - Horizontal scroll on mobile

#### **View Persistence**
- User's preferred view saved per page
- "View as: Table | Cards | List" toggle in top right
- Smooth transition animations between views

### **Forms & Input Design**

#### **Form Layout**
- Clear field labels (above inputs, not placeholder-only)
- Helper text below fields when needed
- Inline validation (real-time feedback)
- Error states with red border + error message
- Success states with green border + checkmark
- Required field indicators (asterisk)
- Multi-step forms with progress indicator

#### **Smart Form Features**
- Auto-save drafts (for quotes, tickets, etc.)
- Unsaved changes warning before leaving page
- Conditional fields (show/hide based on selections)
- Dependent dropdowns (e.g., country → state → city)
- File upload with drag-drop zones
- Rich text editor for descriptions (Tiptap or similar)
- Date pickers with keyboard shortcuts

### **Notifications & Feedback**

#### **Toast Notifications**
- **Position:** Top-right corner
- **Auto-dismiss:** 5 seconds (hover to pause)
- **Types:**
  - Success (green, checkmark icon)
  - Error (red, X icon)
  - Warning (yellow, alert icon)
  - Info (blue, info icon)
- **Stacking:** Multiple toasts stack vertically
- **Actions:** Optional action button (e.g., "Undo", "View")

#### **Loading States**
- Skeleton loaders for content areas (not spinners)
- Progress bars for file uploads
- Button loading state (spinner + disabled)
- Optimistic UI updates where possible
- Global loading indicator for page transitions (thin bar at top)

#### **Empty States**
- Friendly illustration or icon
- Clear message explaining why empty
- Primary CTA to add first item
- Example data "Try adding your first..." prompt

### **Animation & Transitions**

#### **Subtle & Professional Approach**
- **Page transitions:** Fade in (150ms) for route changes
- **Modal/drawer:** Slide in from right (200ms ease-out)
- **Dropdown menus:** Fade + scale up (100ms)
- **Hover states:** Color transition (150ms)
- **Drag-drop:** Smooth transform with drop shadow
- **Loading skeletons:** Subtle shimmer effect
- **No excessive bounce/elastic** - professional feel
- **Respect `prefers-reduced-motion`** system setting

### **Mobile Experience**

#### **Desktop-Optimized with PWA Mobile App**

**Desktop (Primary Experience):**
- Full sidebar navigation
- Rich data tables with all columns
- Multi-panel layouts
- Keyboard shortcuts
- Hover states and tooltips
- 1400px+ optimized layouts

**Tablet (Responsive):**
- Collapsible sidebar (auto-collapse on tablets)
- Table horizontal scroll with sticky columns
- Touch-friendly tap targets (44px minimum)
- Simplified filters (slide-in panel)

**Mobile (PWA - Progressive Web App):**
- **Bottom tab navigation** (replaces sidebar)
- **Card-based layouts** (no complex tables on phone)
- **Swipe gestures** (swipe to delete, swipe between tabs)
- **Native-like animations** (smooth, instant feedback)
- **Offline capability** (view cached data, queue actions)
- **Push notifications** (for urgent tickets, job updates)
- **Add to home screen** support
- **Camera integration** for photo uploads from field
- **GPS location** for job clock-in/out

**Engineer Mobile App Features:**
- Simple job list (today's schedule)
- Clock in/out with GPS verification
- Photo upload from site
- Update job status (In Progress, Blocked, Complete)
- Quick notes and time entry
- Offline-first architecture (sync when back online)

### **Accessibility**

#### **WCAG 2.1 AA Compliance**
- Keyboard navigation for all interactive elements
- Focus indicators clearly visible
- Color contrast ratios meet AA standards
- Alt text for all images and icons
- ARIA labels for screen readers
- Skip to main content link
- Form error announcements
- Semantic HTML structure

### **Performance & UX**

#### **Fast & Responsive**
- **Initial page load:** < 2 seconds
- **Navigation transitions:** < 200ms
- **Livewire updates:** Instant with wire:loading states
- **Image optimization:** Lazy loading, WebP format, responsive images
- **Code splitting:** Load only what's needed per page
- **Database query optimization:** Eager loading, caching
- **CDN for static assets**

#### **User Feedback**
- Instant visual feedback on all actions
- Optimistic UI updates (assume success, rollback on error)
- Clear error messages with recovery suggestions
- Confirmation dialogs for destructive actions
- Auto-save indicators ("Saved 2 minutes ago")

### **Navigation Structure**

#### **Staff Navigation**
**Main Sections:**
- **Dashboard** (home icon)
- **Sales**
  - Quotes
  - Opportunities
  - Products
- **Operations**
  - Jobs
  - Projects (Kanban/Gantt)
  - Schedule
  - Timesheets
- **Service**
  - Tickets
  - Assets
- **Customers**
  - Customer List
  - Contacts
- **Content**
  - Blog Posts
  - Pages
- **Reports**
  - Revenue
  - Performance
  - Activity Logs
- **Settings**
  - Team Settings
  - Users & Roles
  - Integrations
  - Branding

#### **Customer Navigation**
- **Dashboard** (overview)
- **My Tickets** (support)
- **My Assets** (assigned assets)
- **Projects** (active jobs)
- **Invoices** (billing)
- **Team** (manage users)
- **Settings** (profile, preferences)

### **Admin Panel (Filament)**

**Purpose:** Backend management for admins only

**Features:**
- Full CRUD for all entities
- Bulk operations
- Advanced filtering and search
- Data export (Excel, CSV, PDF)
- Activity log review
- System settings configuration
- User and team management
- API key management
- Integration setup

**Design:** Filament's default professional UI with custom branding colors

---

### **Design Inspiration Examples**

To achieve the professional SaaS aesthetic, reference these patterns:

1. **Stripe Dashboard** - Clean data presentation, excellent use of whitespace
2. **Linear** - Fast, keyboard-first, minimal animations
3. **HubSpot** - Professional business feel, great data visualization
4. **Attio CRM** - Modern design patterns, flexible views
5. **Notion** - Customizable layouts, great empty states

**Key Takeaways:**
- Clean, uncluttered interfaces
- Data-first design
- Thoughtful use of color (not too vibrant)
- Consistent spacing and typography
- Fast, responsive interactions

---

## **Security & Compliance**

### **Security Measures**
- CSRF protection (Laravel default)
- XSS prevention (Blade escaping)
- SQL injection protection (Eloquent ORM)
- Rate limiting on API and login
- Two-factor authentication (Jetstream built-in)
- API key rotation
- Activity logging for audit trails

### **Data Protection**
- Encrypt sensitive data (licenses, credentials)
- Secure file storage permissions
- GDPR compliance considerations (data export/deletion)
- Regular backups (Spatie Backup package)

---

## **Phase 1 Implementation Priority**

**Suggested build order:**

1. **Foundation (Week 1-2)**
   - Laravel + Jetstream + Livewire Volt setup
   - Docker environment
   - Database schema design
   - Multi-tenancy decision and implementation
   - Basic auth and team setup

2. **Core Modules (Week 3-6)**
   - User dashboard (staff + customer)
   - Product catalog
   - Quotation system (basic)
   - Asset management (basic CRUD)
   - File uploads (Spatie Media Library)

3. **Job Management (Week 7-9)**
   - Job/project creation
   - Kanban boards
   - Engineer assignment
   - Timesheet tracking
   - Basic mobile view for engineers

4. **Ticketing & Communication (Week 10-11)**
   - Ticket system
   - Email notifications
   - Real-time updates (Pusher)
   - Customer portal access

5. **Advanced Features (Week 12-14)**
   - Quote approval workflow
   - Gantt charts
   - Resource management
   - Asset expiry tracking
   - Invoice PDF generation

6. **Integrations & API (Week 15-16)**
   - RESTful API endpoints
   - Google Services integration
   - Accounting software hooks
   - Webhook system

7. **Polish & Testing (Week 17-18)**
   - Full test suite
   - UI/UX refinements
   - Performance optimization
   - Documentation
   - CI/CD pipeline

---

## **Open Questions for Multi-Tenancy Approach**

**You mentioned "not decided yet" on multi-tenancy. Here are the recommendations:**

### **Option 1: Jetstream Teams (Recommended)**
**Pros:**
- Already built into Jetstream
- Simple implementation
- Single database (easier management)
- Good for your use case (staff + customers in one system)

**Cons:**
- All data in one database (less isolation)
- Requires careful query scoping to prevent data leaks
- May need manual tenant filtering

**Best for:** Your scenario where staff manages multiple customer accounts and customers have limited access to their own data.

### **Option 2: Spatie Multi-tenancy + Single Database**
**Pros:**
- Automatic tenant scoping
- Flexible architecture
- Can add domain-based tenancy later
- Works well with Jetstream

**Cons:**
- Additional package to learn
- Still single database

**Best for:** Future scalability if you want to add domain-based multi-tenancy later (e.g., customer1.yourapp.com).

### **Option 3: Tenancy for Laravel (Separate Databases)**
**Pros:**
- Complete data isolation
- Most secure approach
- Can scale tenants independently

**Cons:**
- Complex migrations and backups
- More server resources
- Harder to do cross-tenant reporting

**Best for:** If you need complete isolation for compliance or if tenants are completely separate businesses.

**My Recommendation:** Start with **Option 1 (Jetstream Teams)** since:
- Staff and customers are in the same system (not completely isolated businesses)
- Staff need to see across customers for management
- Simpler to implement and maintain
- Can always migrate to separate databases later if needed

---

## **Estimated Timeline**
- **MVP (Core features):** 10-12 weeks
- **Full Feature Set:** 16-18 weeks
- **Testing & Refinement:** 2-3 weeks
- **Total:** ~20 weeks (5 months) for full implementation

---

## **Next Steps**

1. **Confirm multi-tenancy approach** (Jetstream Teams recommended)
2. **Finalize database schema** based on all modules
3. **Set up development environment** (Docker + Sail)
4. **Create initial Laravel project** with all packages
5. **Begin Phase 1 implementation**

---

**This design brief provides a comprehensive foundation for building your Laravel field service management system!**
